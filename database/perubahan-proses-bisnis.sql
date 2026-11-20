-- =====================================================================
--  PROSES BISNIS: pengadaan -> penerimaan -> (retur) -> penjualan
--  Dipasang dengan:  php artisan db:pasang-proses-bisnis
--  Aman dijalankan berulang kali. Tiap blok dipisah baris "-- @@ judul".
--
--  Sudah ada dari awal (TIDAK diubah):
--    FN 1 fn_cek_sisa_stok      FN 2 fn_hitung_ppn11
--    SP 1 sp_tambah_barang      SP 2 sp_nonaktifkan_barang
--    TG 1 trg_after_insert_detail_penerimaan
--  Ditambahkan file ini: FN 3-4, SP 3-12, TG 2-3  (daftar lengkap: database/DAFTAR-SP-FN-TG.md)
--
--  Status pengadaan : P (proses)   -> S (selesai)
--  Status penerimaan: P (sebagian) -> T (tuntas)
--  kartu_stok.jenis_transaksi: M (penerimaan), J (penjualan), R (retur)
-- =====================================================================

-- @@ 1. Data lama: penerimaan berstatus S (versi awal) dianggap tuntas
UPDATE `penerimaan` SET `status` = 'T' WHERE `status` = 'S';

-- @@ 2. FN 3 (hapus versi lama): fn_sisa_pesanan
DROP FUNCTION IF EXISTS `fn_sisa_pesanan`;

-- @@ 3. FN 3: fn_sisa_pesanan = dipesan - diterima + diretur
CREATE FUNCTION `fn_sisa_pesanan`(p_idpengadaan BIGINT, p_idbarang INT) RETURNS int
    READS SQL DATA
BEGIN
    DECLARE v_pesan INT DEFAULT 0;
    DECLARE v_terima INT DEFAULT 0;
    DECLARE v_retur INT DEFAULT 0;

    SELECT COALESCE(SUM(jumlah), 0) INTO v_pesan
    FROM detail_pengadaan
    WHERE idpengadaan = p_idpengadaan AND idbarang = p_idbarang;

    SELECT COALESCE(SUM(dt.jumlah_terima), 0) INTO v_terima
    FROM detail_penerimaan dt
    JOIN penerimaan pn ON pn.idpenerimaan = dt.idpenerimaan
    WHERE pn.idpengadaan = p_idpengadaan AND dt.barang_idbarang = p_idbarang;

    SELECT COALESCE(SUM(dr.jumlah), 0) INTO v_retur
    FROM detail_retur dr
    JOIN detail_penerimaan dt ON dt.iddetail_penerimaan = dr.iddetail_penerimaan
    JOIN penerimaan pn ON pn.idpenerimaan = dt.idpenerimaan
    WHERE pn.idpengadaan = p_idpengadaan AND dt.barang_idbarang = p_idbarang;

    RETURN v_pesan - v_terima + v_retur;
END;

-- @@ 4. FN 4 (hapus versi lama): fn_harga_jual
DROP FUNCTION IF EXISTS `fn_harga_jual`;

-- @@ 5. FN 4: fn_harga_jual = harga dasar + margin yang aktif
CREATE FUNCTION `fn_harga_jual`(p_idbarang INT) RETURNS int
    READS SQL DATA
BEGIN
    DECLARE v_dasar INT;
    DECLARE v_persen DOUBLE DEFAULT 0;

    -- harga dasar = harga terima terakhir dari penerimaan; jika belum ada, harga barang
    SET v_dasar = (SELECT harga_satuan_terima FROM detail_penerimaan
                   WHERE barang_idbarang = p_idbarang
                   ORDER BY iddetail_penerimaan DESC LIMIT 1);
    IF v_dasar IS NULL THEN
        SET v_dasar = (SELECT harga FROM barang WHERE idbarang = p_idbarang);
    END IF;

    SET v_persen = COALESCE((SELECT persen FROM margin_penjualan
                             WHERE status = 1
                             ORDER BY idmargin_penjualan DESC LIMIT 1), 0);

    RETURN ROUND(COALESCE(v_dasar, 0) * (1 + v_persen / 100));
END;

-- @@ 6. SP 3 (hapus versi lama): sp_buat_pengadaan
DROP PROCEDURE IF EXISTS `sp_buat_pengadaan`;

-- @@ 7. SP 3: sp_buat_pengadaan = header pengadaan (status P = proses)
CREATE PROCEDURE `sp_buat_pengadaan`(IN p_user INT, IN p_vendor INT, OUT p_idpengadaan BIGINT)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM vendor WHERE idvendor = p_vendor AND status = '1') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Vendor tidak ditemukan atau tidak aktif.';
    END IF;

    INSERT INTO pengadaan (`timestamp`, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai, user_iduser)
    VALUES (NOW(), 'P', p_vendor, 0, 0, 0, p_user);

    SET p_idpengadaan = LAST_INSERT_ID();
END;

-- @@ 8. SP 4 (hapus versi lama): sp_tambah_detail_pengadaan
DROP PROCEDURE IF EXISTS `sp_tambah_detail_pengadaan`;

-- @@ 9. SP 4: sp_tambah_detail_pengadaan = detail pengadaan, PPN lewat FN 2, total dihitung ulang
CREATE PROCEDURE `sp_tambah_detail_pengadaan`(
    IN p_idpengadaan BIGINT, IN p_idbarang INT, IN p_jumlah INT, IN p_harga INT)
BEGIN
    DECLARE v_status CHAR(1);
    DECLARE v_sub INT DEFAULT 0;
    DECLARE v_ppn INT DEFAULT 0;

    SELECT status INTO v_status FROM pengadaan WHERE idpengadaan = p_idpengadaan;

    IF v_status IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengadaan tidak ditemukan.';
    END IF;
    IF v_status <> 'P' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengadaan sudah selesai, barang tidak bisa ditambah.';
    END IF;
    IF p_jumlah < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah barang minimal 1.';
    END IF;
    IF p_harga < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Harga satuan tidak boleh negatif.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM barang WHERE idbarang = p_idbarang AND status = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang tidak ditemukan atau tidak aktif.';
    END IF;

    INSERT INTO detail_pengadaan (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
    VALUES (p_harga, p_jumlah, p_harga * p_jumlah, p_idbarang, p_idpengadaan);

    SELECT COALESCE(SUM(sub_total), 0) INTO v_sub
    FROM detail_pengadaan WHERE idpengadaan = p_idpengadaan;

    SET v_ppn = fn_hitung_ppn11(v_sub);

    UPDATE pengadaan
    SET subtotal_nilai = v_sub, ppn = v_ppn, total_nilai = v_sub + v_ppn
    WHERE idpengadaan = p_idpengadaan;
END;

-- @@ 10. SP 5 (hapus versi lama): sp_buat_penerimaan
DROP PROCEDURE IF EXISTS `sp_buat_penerimaan`;

-- @@ 11. SP 5: sp_buat_penerimaan = header penerimaan (status awal P = sebagian)
CREATE PROCEDURE `sp_buat_penerimaan`(IN p_idpengadaan BIGINT, IN p_user INT, OUT p_idpenerimaan BIGINT)
BEGIN
    DECLARE v_status CHAR(1);

    SELECT status INTO v_status FROM pengadaan WHERE idpengadaan = p_idpengadaan;

    IF v_status IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengadaan tidak ditemukan.';
    END IF;
    IF v_status <> 'P' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengadaan ini sudah selesai, tidak bisa diterima lagi.';
    END IF;

    INSERT INTO penerimaan (created_at, status, idpengadaan, iduser)
    VALUES (NOW(), 'P', p_idpengadaan, p_user);

    SET p_idpenerimaan = LAST_INSERT_ID();
END;

-- @@ 12. SP 6 (hapus versi lama): sp_tambah_detail_penerimaan
DROP PROCEDURE IF EXISTS `sp_tambah_detail_penerimaan`;

-- @@ 13. SP 6: sp_tambah_detail_penerimaan = detail penerimaan (jumlah dan harga manual; memicu TG 1)
CREATE PROCEDURE `sp_tambah_detail_penerimaan`(
    IN p_idpenerimaan BIGINT, IN p_idbarang INT, IN p_jumlah INT, IN p_harga INT)
BEGIN
    DECLARE v_idpengadaan BIGINT;
    DECLARE v_ada INT DEFAULT 0;
    DECLARE v_sisa INT DEFAULT 0;
    DECLARE v_pesan VARCHAR(200);

    SELECT idpengadaan INTO v_idpengadaan FROM penerimaan WHERE idpenerimaan = p_idpenerimaan;

    IF v_idpengadaan IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerimaan tidak ditemukan.';
    END IF;
    IF p_jumlah < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah diterima minimal 1.';
    END IF;
    IF p_harga < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Harga satuan terima tidak boleh negatif.';
    END IF;

    SELECT COUNT(*) INTO v_ada FROM detail_pengadaan
    WHERE idpengadaan = v_idpengadaan AND idbarang = p_idbarang;
    IF v_ada = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang ini bukan bagian dari pengadaan tersebut.';
    END IF;

    SET v_sisa = fn_sisa_pesanan(v_idpengadaan, p_idbarang);
    IF p_jumlah > v_sisa THEN
        SET v_pesan = CONCAT('Jumlah diterima melebihi sisa pesanan (sisa: ', v_sisa, ').');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_pesan;
    END IF;

    -- Barang yang belum punya riwayat stok diberi saldo awal 0 (agar trigger penerimaan tidak mendapat NULL).
    IF NOT EXISTS (SELECT 1 FROM kartu_stok WHERE idbarang = p_idbarang) THEN
        INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, idtransaksi, idbarang)
        VALUES ('M', 0, 0, 0, 0, p_idbarang);
    END IF;

    INSERT INTO detail_penerimaan
        (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
    VALUES
        (p_idpenerimaan, p_idbarang, p_jumlah, p_harga, p_jumlah * p_harga);
END;

-- @@ 14. SP 7 (hapus versi lama): sp_tutup_penerimaan
DROP PROCEDURE IF EXISTS `sp_tutup_penerimaan`;

-- @@ 15. SP 7: sp_tutup_penerimaan = status tuntas/sebagian; bila tuntas pengadaan selesai
CREATE PROCEDURE `sp_tutup_penerimaan`(IN p_idpenerimaan BIGINT)
BEGIN
    DECLARE v_idpengadaan BIGINT;
    DECLARE v_belum INT DEFAULT 0;

    SELECT idpengadaan INTO v_idpengadaan FROM penerimaan WHERE idpenerimaan = p_idpenerimaan;

    IF v_idpengadaan IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerimaan tidak ditemukan.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM detail_penerimaan WHERE idpenerimaan = p_idpenerimaan) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerimaan belum berisi barang.';
    END IF;

    SELECT COUNT(*) INTO v_belum
    FROM detail_pengadaan dp
    WHERE dp.idpengadaan = v_idpengadaan
      AND fn_sisa_pesanan(dp.idpengadaan, dp.idbarang) > 0;

    IF v_belum = 0 THEN
        UPDATE penerimaan SET status = 'T' WHERE idpenerimaan = p_idpenerimaan;
        UPDATE pengadaan SET status = 'S' WHERE idpengadaan = v_idpengadaan;
    ELSE
        UPDATE penerimaan SET status = 'P' WHERE idpenerimaan = p_idpenerimaan;
    END IF;
END;

-- @@ 16. SP 8 (hapus versi lama): sp_buat_retur
DROP PROCEDURE IF EXISTS `sp_buat_retur`;

-- @@ 17. SP 8: sp_buat_retur = header retur
CREATE PROCEDURE `sp_buat_retur`(IN p_idpenerimaan BIGINT, IN p_user INT, OUT p_idretur BIGINT)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM penerimaan WHERE idpenerimaan = p_idpenerimaan) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerimaan tidak ditemukan.';
    END IF;

    INSERT INTO retur (created_at, idpenerimaan, iduser)
    VALUES (NOW(), p_idpenerimaan, p_user);

    SET p_idretur = LAST_INSERT_ID();
END;

-- @@ 18. SP 9 (hapus versi lama): sp_tambah_detail_retur
DROP PROCEDURE IF EXISTS `sp_tambah_detail_retur`;

-- @@ 19. SP 9: sp_tambah_detail_retur = detail retur (memicu TG 3)
CREATE PROCEDURE `sp_tambah_detail_retur`(
    IN p_idretur BIGINT, IN p_iddetail_penerimaan BIGINT, IN p_jumlah INT, IN p_alasan VARCHAR(200))
BEGIN
    DECLARE v_pn_retur BIGINT;
    DECLARE v_pn_detail BIGINT;
    DECLARE v_barang INT;
    DECLARE v_terima INT;
    DECLARE v_sudah INT DEFAULT 0;
    DECLARE v_pesan VARCHAR(200);

    SELECT idpenerimaan INTO v_pn_retur FROM retur WHERE idretur = p_idretur;
    IF v_pn_retur IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Retur tidak ditemukan.';
    END IF;

    SELECT idpenerimaan, barang_idbarang, jumlah_terima INTO v_pn_detail, v_barang, v_terima
    FROM detail_penerimaan WHERE iddetail_penerimaan = p_iddetail_penerimaan;
    IF v_pn_detail IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Detail penerimaan tidak ditemukan.';
    END IF;
    IF v_pn_detail <> v_pn_retur THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang ini bukan bagian dari penerimaan yang diretur.';
    END IF;
    IF p_jumlah < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur minimal 1.';
    END IF;
    IF p_alasan IS NULL OR TRIM(p_alasan) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Alasan retur wajib diisi.';
    END IF;

    SET v_sudah = COALESCE((SELECT SUM(jumlah) FROM detail_retur
                            WHERE iddetail_penerimaan = p_iddetail_penerimaan), 0);
    IF p_jumlah > v_terima - v_sudah THEN
        SET v_pesan = CONCAT('Jumlah retur melebihi barang yang diterima (maksimal: ', v_terima - v_sudah, ').');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_pesan;
    END IF;
    IF fn_cek_sisa_stok(v_barang) < p_jumlah THEN
        SET v_pesan = CONCAT('Stok tidak cukup untuk diretur (stok saat ini: ', fn_cek_sisa_stok(v_barang), ').');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_pesan;
    END IF;

    INSERT INTO detail_retur (jumlah, alasan, idretur, iddetail_penerimaan)
    VALUES (p_jumlah, TRIM(p_alasan), p_idretur, p_iddetail_penerimaan);
END;

-- @@ 20. SP 10 (hapus versi lama): sp_buat_penjualan
DROP PROCEDURE IF EXISTS `sp_buat_penjualan`;

-- @@ 21. SP 10: sp_buat_penjualan = header penjualan, menarik margin aktif
CREATE PROCEDURE `sp_buat_penjualan`(IN p_user INT, OUT p_idpenjualan INT)
BEGIN
    DECLARE v_margin INT;

    SET v_margin = (SELECT idmargin_penjualan FROM margin_penjualan
                    WHERE status = 1 ORDER BY idmargin_penjualan DESC LIMIT 1);

    IF v_margin IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Belum ada margin penjualan yang aktif. Aktifkan satu margin dulu.';
    END IF;

    INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan, status)
    VALUES (NOW(), 0, 0, 0, p_user, v_margin, 1);

    SET p_idpenjualan = LAST_INSERT_ID();
END;

-- @@ 22. SP 11 (hapus versi lama): sp_tambah_detail_penjualan
DROP PROCEDURE IF EXISTS `sp_tambah_detail_penjualan`;

-- @@ 23. SP 11: sp_tambah_detail_penjualan = detail penjualan, harga lewat FN 4 (memicu TG 2)
CREATE PROCEDURE `sp_tambah_detail_penjualan`(
    IN p_idpenjualan INT, IN p_idbarang INT, IN p_jumlah INT)
BEGIN
    DECLARE v_harga INT;
    DECLARE v_sub INT DEFAULT 0;
    DECLARE v_ppn INT DEFAULT 0;
    DECLARE v_stok INT DEFAULT 0;
    DECLARE v_pesan VARCHAR(200);

    IF NOT EXISTS (SELECT 1 FROM penjualan WHERE idpenjualan = p_idpenjualan) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penjualan tidak ditemukan.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM barang WHERE idbarang = p_idbarang AND status = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang tidak ditemukan atau tidak aktif.';
    END IF;
    IF p_jumlah < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah jual minimal 1.';
    END IF;

    SET v_stok = fn_cek_sisa_stok(p_idbarang);
    IF v_stok < p_jumlah THEN
        SET v_pesan = CONCAT('Stok tidak cukup (stok saat ini: ', v_stok, ').');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_pesan;
    END IF;

    SET v_harga = fn_harga_jual(p_idbarang);

    INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
    VALUES (v_harga, p_jumlah, v_harga * p_jumlah, p_idpenjualan, p_idbarang);

    SELECT COALESCE(SUM(subtotal), 0) INTO v_sub
    FROM detail_penjualan WHERE penjualan_idpenjualan = p_idpenjualan;

    SET v_ppn = fn_hitung_ppn11(v_sub);

    UPDATE penjualan
    SET subtotal_nilai = v_sub, ppn = v_ppn, total_nilai = v_sub + v_ppn
    WHERE idpenjualan = p_idpenjualan;
END;

-- @@ 24. SP 12 (hapus versi lama): sp_aktifkan_margin
DROP PROCEDURE IF EXISTS `sp_aktifkan_margin`;

-- @@ 25. SP 12: sp_aktifkan_margin = hanya SATU margin yang boleh aktif
CREATE PROCEDURE `sp_aktifkan_margin`(IN p_id INT)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM margin_penjualan WHERE idmargin_penjualan = p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Margin tidak ditemukan.';
    END IF;

    UPDATE margin_penjualan SET status = 0, updated_at = NOW()
    WHERE status = 1 AND idmargin_penjualan <> p_id;

    UPDATE margin_penjualan SET status = 1, updated_at = NOW()
    WHERE idmargin_penjualan = p_id;
END;

-- @@ 26. TG 2 (hapus versi lama): trg_after_insert_detail_penjualan
DROP TRIGGER IF EXISTS `trg_after_insert_detail_penjualan`;

-- @@ 27. TG 2: trg_after_insert_detail_penjualan = stok berkurang di kartu_stok (jenis J)
CREATE TRIGGER `trg_after_insert_detail_penjualan`
AFTER INSERT ON `detail_penjualan`
FOR EACH ROW
BEGIN
    DECLARE v_stok_lama INT DEFAULT 0;

    SET v_stok_lama = COALESCE((SELECT stock FROM kartu_stok
                                WHERE idbarang = NEW.idbarang
                                ORDER BY idkartu_stok DESC LIMIT 1), 0);

    INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, idtransaksi, idbarang)
    VALUES ('J', 0, NEW.jumlah, v_stok_lama - NEW.jumlah, NEW.penjualan_idpenjualan, NEW.idbarang);

    UPDATE barang SET stok = fn_cek_sisa_stok(NEW.idbarang) WHERE idbarang = NEW.idbarang;
END;

-- @@ 28. TG 3 (hapus versi lama): trg_after_insert_detail_retur
DROP TRIGGER IF EXISTS `trg_after_insert_detail_retur`;

-- @@ 29. TG 3: trg_after_insert_detail_retur = stok berkurang (jenis R), pengadaan dibuka lagi bila kurang
CREATE TRIGGER `trg_after_insert_detail_retur`
AFTER INSERT ON `detail_retur`
FOR EACH ROW
BEGIN
    DECLARE v_barang INT;
    DECLARE v_pengadaan BIGINT;
    DECLARE v_stok_lama INT DEFAULT 0;

    SELECT dt.barang_idbarang, pn.idpengadaan INTO v_barang, v_pengadaan
    FROM detail_penerimaan dt
    JOIN penerimaan pn ON pn.idpenerimaan = dt.idpenerimaan
    WHERE dt.iddetail_penerimaan = NEW.iddetail_penerimaan;

    SET v_stok_lama = COALESCE((SELECT stock FROM kartu_stok
                                WHERE idbarang = v_barang
                                ORDER BY idkartu_stok DESC LIMIT 1), 0);

    INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, idtransaksi, idbarang)
    VALUES ('R', 0, NEW.jumlah, v_stok_lama - NEW.jumlah, NEW.idretur, v_barang);

    UPDATE barang SET stok = fn_cek_sisa_stok(v_barang) WHERE idbarang = v_barang;

    -- barang yang diretur harus dikirim ulang vendor -> pengadaan kembali berstatus proses
    UPDATE pengadaan SET status = 'P' WHERE idpengadaan = v_pengadaan AND status = 'S';
END;

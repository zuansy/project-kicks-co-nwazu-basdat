-- Jalankan SEKALI di HeidiSQL (database zuandb), tab Query, lalu F9.
-- Tujuan: login memakai email + pendaftaran publik (role Pelanggan).

-- 1) Kolom email pada tabel user (unik).
ALTER TABLE `user`
    ADD COLUMN `email` VARCHAR(100) NULL AFTER `username`,
    ADD UNIQUE KEY `uq_user_email` (`email`);

-- 2) Role baru untuk pembeli/publik (dilewati jika sudah ada).
INSERT INTO `role` (`nama_role`)
SELECT 'Pelanggan'
WHERE NOT EXISTS (SELECT 1 FROM `role` WHERE `nama_role` = 'Pelanggan');

-- 3) Email untuk akun karyawan yang sudah ada (boleh diganti sesukamu).
UPDATE `user` SET `email` = 'admin@nwazukicks.test'   WHERE `username` = 'admin_satu';
UPDATE `user` SET `email` = 'budi@nwazukicks.test'    WHERE `username` = 'kasir_budi';
UPDATE `user` SET `email` = 'agus@nwazukicks.test'    WHERE `username` = 'gudang_agus';
UPDATE `user` SET `email` = 'rina@nwazukicks.test'    WHERE `username` = 'manajer_rina';
UPDATE `user` SET `email` = 'sri@nwazukicks.test'     WHERE `username` = 'owner_sri';

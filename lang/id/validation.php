<?php

return [
    'array'    => ':attribute harus berupa daftar.',
    'distinct' => ':attribute tidak boleh dipilih dua kali.',
    'exists'   => ':attribute yang dipilih tidak valid.',
    'in'       => ':attribute yang dipilih tidak valid.',
    'integer'  => ':attribute harus berupa bilangan bulat.',
    'max'      => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'string'  => ':attribute tidak boleh lebih dari :max karakter.',
        'array'   => ':attribute tidak boleh lebih dari :max item.',
    ],
    'min'      => [
        'numeric' => ':attribute minimal :min.',
        'string'  => ':attribute minimal :min karakter.',
        'array'   => ':attribute minimal :min item.',
    ],
    'required' => ':attribute wajib diisi.',
    'string'   => ':attribute harus berupa teks.',

    'attributes' => [
        'nama'             => 'Nama barang',
        'jenis'            => 'Jenis',
        'idsatuan'         => 'Satuan',
        'harga'            => 'Harga',
        'status'           => 'Status',
        'username'         => 'Username',
        'password'         => 'Password',
        'vendor_idvendor'  => 'Vendor',
        'item'             => 'Daftar barang',
        'item.*.idbarang'  => 'Barang',
        'item.*.jumlah'    => 'Jumlah',
        'item.*.harga'     => 'Harga satuan',
        'idpengadaan'      => 'Pengadaan',
        'terima'           => 'Jumlah diterima',
        'terima.*'         => 'Jumlah diterima',
    ],
];

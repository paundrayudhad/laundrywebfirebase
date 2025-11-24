<?php
return [
    'outlets' => [
        'outlet_central' => [
            'name' => 'Outlet Pusat',
            'phone' => '021-1234567',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'created_at' => '2024-05-01T08:00:00+07:00',
            'updated_at' => '2024-05-01T08:00:00+07:00'
        ],
        'outlet_branch' => [
            'name' => 'Outlet Cabang',
            'phone' => '021-7654321',
            'address' => 'Jl. Melati No. 8, Bandung',
            'created_at' => '2024-05-02T08:00:00+07:00',
            'updated_at' => '2024-05-02T08:00:00+07:00'
        ]
    ],
    'employees' => [
        'emp_admin' => [
            'name' => 'Admin Utama',
            'email' => 'admin@laundry.test',
            'phone' => '08123456789',
            'role' => 'admin',
            'password' => '$2y$12$ULQzmGeRMtItw9nWDEdSFuV2G9HdMzg4sKzaCn12tpvtDqqqjA63q', // admin123
            'outlet_id' => 'outlet_central',
            'created_at' => '2024-05-03T08:00:00+07:00',
            'updated_at' => '2024-05-03T08:00:00+07:00'
        ],
        'emp_kasir' => [
            'name' => 'Kasir Toko',
            'email' => 'kasir@laundry.test',
            'phone' => '08129876543',
            'role' => 'kasir',
            'password' => '$2y$12$23UqlXDX1d0jGEWRnjWLg.tM4qemuPHn7sVbVCtu.cxHmqPmO3qxq', // kasir123
            'outlet_id' => 'outlet_central',
            'created_at' => '2024-05-03T09:00:00+07:00',
            'updated_at' => '2024-05-03T09:00:00+07:00'
        ],
        'emp_staff' => [
            'name' => 'Staff Operasional',
            'email' => 'staff@laundry.test',
            'phone' => '0812121212',
            'role' => 'staff',
            'password' => '$2y$12$UJk9d6WeK3/W/CsR4IL8me6XWOyrusTYjyxAdQGBOWw7saPp73dFK', // staff123
            'outlet_id' => 'outlet_branch',
            'created_at' => '2024-05-03T10:00:00+07:00',
            'updated_at' => '2024-05-03T10:00:00+07:00'
        ]
    ],
    'customers' => [
        'cust_andi' => [
            'full_name' => 'Andi Saputra',
            'phone' => '0819000001',
            'email' => 'andi@example.com',
            'address' => 'Jl. Mawar No. 10, Jakarta',
            'created_at' => '2024-05-04T08:00:00+07:00',
            'updated_at' => '2024-05-04T08:00:00+07:00'
        ],
        'cust_sinta' => [
            'full_name' => 'Sinta Lestari',
            'phone' => '0819000002',
            'email' => 'sinta@example.com',
            'address' => 'Jl. Dahlia No. 5, Bandung',
            'created_at' => '2024-05-04T09:00:00+07:00',
            'updated_at' => '2024-05-04T09:00:00+07:00'
        ]
    ],
    'laundry_types' => [
        'lt_kiloan' => [
            'name' => 'Cuci Kering Setrika',
            'description' => 'Layanan standar per kilo dengan setrika.',
            'price_per_kg' => 7000,
            'created_at' => '2024-05-05T08:00:00+07:00',
            'updated_at' => '2024-05-05T08:00:00+07:00'
        ],
        'lt_express' => [
            'name' => 'Express 24 Jam',
            'description' => 'Selesai dalam 24 jam dengan prioritas.',
            'price_per_kg' => 12000,
            'created_at' => '2024-05-05T09:00:00+07:00',
            'updated_at' => '2024-05-05T09:00:00+07:00'
        ],
        'lt_bedcover' => [
            'name' => 'Cuci Bed Cover',
            'description' => 'Khusus bed cover dan selimut besar.',
            'price_per_kg' => 15000,
            'created_at' => '2024-05-05T10:00:00+07:00',
            'updated_at' => '2024-05-05T10:00:00+07:00'
        ]
    ],
    'orders' => [
        'order_demo1' => [
            'order_number' => 'ORD-0001',
            'customer_id' => 'cust_andi',
            'employee_id' => 'emp_kasir',
            'outlet_id' => 'outlet_central',
            'laundry_type_id' => 'lt_kiloan',
            'weight_kg' => 5,
            'status' => 'processing',
            'due_date' => '2024-05-10',
            'notes' => 'Gunakan detergen hypoallergenic.',
            'total' => 35000,
            'created_at' => '2024-05-06T08:00:00+07:00',
            'updated_at' => '2024-05-06T08:00:00+07:00'
        ],
        'order_demo2' => [
            'order_number' => 'ORD-0002',
            'customer_id' => 'cust_sinta',
            'employee_id' => 'emp_staff',
            'outlet_id' => 'outlet_branch',
            'laundry_type_id' => 'lt_express',
            'weight_kg' => 3,
            'status' => 'created',
            'due_date' => '2024-05-09',
            'notes' => 'Permintaan ekspres 24 jam.',
            'total' => 36000,
            'created_at' => '2024-05-06T09:00:00+07:00',
            'updated_at' => '2024-05-06T09:00:00+07:00'
        ]
    ],
    'payments' => [
        'payment_demo1' => [
            'order_id' => 'order_demo1',
            'amount' => 20000,
            'method' => 'cash',
            'status' => 'pending',
            'paid_at' => '2024-05-06T10:00:00+07:00'
        ],
        'payment_demo2' => [
            'order_id' => 'order_demo2',
            'amount' => 36000,
            'method' => 'transfer',
            'status' => 'paid',
            'paid_at' => '2024-05-06T11:00:00+07:00'
        ]
    ],
    'queues' => [
        'queue_demo1' => [
            'order_id' => 'order_demo1',
            'outlet_id' => 'outlet_central',
            'status' => 'processing',
            'position' => 1,
            'notes' => 'Sedang dicuci mesin 2.',
            'created_at' => '2024-05-06T08:30:00+07:00'
        ],
        'queue_demo2' => [
            'order_id' => 'order_demo2',
            'outlet_id' => 'outlet_branch',
            'status' => 'waiting',
            'position' => 2,
            'notes' => 'Menunggu antrian ekspres.',
            'created_at' => '2024-05-06T09:30:00+07:00'
        ]
    ]
];

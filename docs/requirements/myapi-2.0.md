My Current Project is in this structure

Project/
├── api-name1/
│   ├── index.php
│   ├── api/index.php
├── api-name2/
│   ├── index.php
│   ├── api/index.php
├── api-name3/
│   ├── index.php
│   └── api/index.php
├─── index.php
└─── README.md

I'd like to change it to this style

Project/
├── public/
│   ├── index.php
│   ├── testpage1.php
│   ├── api-name1-specs.php
│   ├── testpage2.php
│   ├── api-name2-specs.php
│   ├── testpage3.php
│   └── api-name3-specs.php
├── api/
│   ├── api-name1/index.php
│   ├── api-name2/index.php
│   └── api-name3/index.php
└── README.md
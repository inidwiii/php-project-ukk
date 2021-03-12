# Project UKK

Repository ini menampung project UKK dengan tema Pengaduan Masyarakat.

Sebelum menjalankan, silahkan ubah konfigurasi file `.htaccess` pada folder `public/.htaccess` mengubah line seperti dibawah :

```htaccess
RewriteBase /{folder root}
```

Dan juga mengubah konfigurasi pada `config/app.php` pada bagian seperti dibawah :

```php
<?php

return [
	'name' => '{$namaApplikasi}',
	'base' => '{$folderRoot}'
];
```

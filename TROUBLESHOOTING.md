# Troubleshooting Auto Upload Block

## Langkah-langkah untuk mendiagnosis masalah upload

### 1. **Akses halaman debug**
Kunjungi: `http://yoursite.com/blocks/auto_upload/debug.php`

### 2. **Lakukan upgrade dan cache purge**
Kunjungi: `http://yoursite.com/blocks/auto_upload/upgrade.php`
- Klik untuk membersihkan cache
- Pastikan event observers terdaftar

### 3. **Periksa konfigurasi**
- Pastikan "Auto Upload" sudah **ENABLED**
- Periksa API endpoint sudah benar
- Test koneksi API

### 4. **Cek logs**
Kunjungi: `http://yoursite.com/blocks/auto_upload/logs.php`
- Periksa error logs untuk pesan "Auto upload"

### 5. **Test manual upload**
Di halaman debug, gunakan form "Manual Test Upload" untuk test langsung

### 6. **Cek events di database**
```sql
SELECT * FROM mdl_logstore_standard_log 
WHERE eventname LIKE '%file%' 
ORDER BY timecreated DESC 
LIMIT 10;
```

### 7. **Periksa file system**
```sql
SELECT id, filename, filesize, timecreated 
FROM mdl_files 
WHERE filename != '.' 
ORDER BY timecreated DESC 
LIMIT 10;
```

## Checklist Troubleshooting

- [ ] Plugin terinstall dengan benar
- [ ] Auto upload di-enable di settings
- [ ] API endpoint dikonfigurasi
- [ ] Event observers terdaftar (check di upgrade.php)
- [ ] Cache sudah di-purge
- [ ] Test API connection berhasil
- [ ] Upload file ke course sebagai test
- [ ] Check logs untuk aktivitas
- [ ] Pastikan course_id valid (> 0)

## Kemungkinan Penyebab

1. **Plugin belum terinstall dengan benar**
   - Kunjungi Site Administration → Notifications
   - Install/upgrade plugin

2. **Auto upload disabled**
   - Site Administration → Plugins → Blocks → Auto Upload
   - Enable "Enable auto upload"

3. **Event observers tidak terdaftar**
   - Kunjungi upgrade.php untuk purge cache
   - Restart web server jika perlu

4. **API endpoint tidak accessible**
   - Test koneksi dari debug.php
   - Check firewall/network

5. **File upload tidak trigger event**
   - Coba berbagai jenis upload (resource, assignment)
   - Check event logs di debug.php

6. **Context tidak valid**
   - Pastikan upload di course yang valid
   - Check course_id di logs

## File Logs Locations

- **Windows XAMPP**: `C:\xampp\apache\logs\error.log`
- **Linux**: `/var/log/apache2/error.log` atau `/var/log/httpd/error_log`
- **PHP logs**: Check `php.ini` untuk `error_log` setting

## Test Commands

### Test API dengan curl:
```bash
curl -X POST http://165.22.62.163:5000/uploads \
  -F "course_id=1" \
  -F "module_id=0" \
  -F "file=@testfile.txt"
```

### Monitor logs real-time:
```bash
tail -f /path/to/error.log | grep "Auto upload"
```

## Jika masih bermasalah

1. Enable debugging di Moodle
2. Check semua logs
3. Test dengan file kecil terlebih dahulu
4. Pastikan API endpoint bisa menerima multipart/form-data
5. Check permission file system Moodle
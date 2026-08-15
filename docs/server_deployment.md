# طريقة تثبيت التحديثات على السيرفر (Production Server)

لكي تقوم بتثبيت التحديث الأخير (خاصية استخراج الـ PDF لتقارير المدرسين) على السيرفر الخاص بك، ستحتاج إلى رفع الملفات التي قمنا بتعديلها، بالإضافة إلى تشغيل أمر `composer` لتثبيت مكتبة الـ PDF التي أضفناها.

إليك الخطوات بالتفصيل التي يجب تنفيذها داخل السيرفر:

## 1. رفع الملفات الجديدة والمعدلة
قم برفع هذه الملفات من جهازك (Local) إلى نفس المسار في السيرفر (باستخدام FTP أو SSH/SCP):
- `composer.json`
- `composer.lock`
- `routes/accountant.php`
- `app/Http/Controllers/Accountant/TeacherHourController.php`
- `app/Services/TeacherHoursService.php`
- `resources/views/accountant/teacher-hours/show.blade.php`
- `resources/views/accountant/teacher-hours/pdf.blade.php` (ملف جديد)

*(إذا كنت تستخدم `git`، يمكنك فقط عمل `git commit` و `git push` من جهازك، وبعدها `git pull` داخل السيرفر).*

## 2. تثبيت مكتبة الـ PDF
بعد رفع الملفات، قم بفتح سطر الأوامر (Terminal/SSH) داخل مجلد المشروع في السيرفر، وقم بتشغيل الأمر التالي لتثبيت مكتبة `dompdf`:
```bash
composer install --no-dev --optimize-autoloader
```

## 3. تنظيف وتحديث الكاش (Cache)
حتى يتعرف السيرفر على المسار (Route) الجديد الذي أضفناه والملفات الجديدة، قم بتشغيل هذه الأوامر بالترتيب:
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

بمجرد الانتهاء من هذه الخطوات الثلاث، سيعمل زر **Export PDF** على السيرفر بنجاح!

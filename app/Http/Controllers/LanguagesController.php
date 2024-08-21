<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;


class LanguagesController
{
    public static function getLocationTranslations()
{
        return [
            'Damascus' => 'دمشق',
            'Rif Damascus' => 'ريف دمشق',
            'Homs' => 'حمص',
            'Aleppo' => 'حلب',
            'Tartus' => 'طرطوس',
            'Latakia' => 'اللاذقية',
            'Hama' => 'حماه',
            'As-Suwayda' => 'السويداء',
            'Daraa' => 'درعا',
            'Deir Ez-Zor' => 'دير الزور',
            'Al-Hasakah' => 'الحسكة',
            'Idlib' => 'إدلب',
            'Ar-Raqqah' => 'الرقة',
            'Quneitra' => 'القنيطرة',
        ];
}

    public static function getTopicTranslations()
{
    return [
        'Administration/Operations/Management' => 'إدارة / عمليات',
        'Data Entry/Archiving' => 'إدخال بيانات / الأرشفة',
        'Strategy/Consulting' => 'تخطيط / مستشار',
        'Research And Development/Statistics/Analyst' => 'البحث والتطوير / إحصائيات / المحلل',
        'IT/Software Development' => 'IT / تطوير البرمجيات',
        'Banking/Insurance' => 'الخدمات المصرفية / تأمين',
        'House Keeping/Office Boys/Porters' => 'التدبير المنزلي / ساعي المكتب / بواب',
        'Translation/Writing/Editorial' => 'ترجمة / كتابة / تحرير',
        'Marketing/PR/Advertising' => 'تسويق / PR / دعاية',
        'Graphic Design/Animation/Art' => 'التصميم الجرافيكي / الرسوم المتحركة / الفن',
        'Education/Teaching/Training' => 'تعليم / تدريس / تدريب',
        'Social Media/Journalism/Publishing' => 'وسائل التواصل الأجتماعي / الصحافة / نشر',
        'Quality' => 'الجودة',
        'Safety/Guard Services' => 'أمان / خدمات الحراسة',
        'Customer Service/Support' => 'خدمة الزبائن / الدعم',
        'Manufacturing/Production' => 'تصنيع / إنتاج',
        'Sport/Nutrition/Physiotherapy' => 'رياضة / تغذية / العلاج الطبيعي',
        'Farming And Agriculture' => 'الزراعة',
        'Drivers/Delivery' => 'سائق / توصيل طلبات',
        'Secretarial/Receptionist' => 'سكرتارية / موظف الإستقبال',
        'Tourism/Travel/Hotels' => 'السياحة / السفر / الفنادق',
        'Pharmaceutical' => 'الادوية',
        'Medical/Healthcare/Nursing' => 'الطب / الرعاية الصحية / التمريض',
        'Dentists/Prosthodontics' => 'طب الأسنان / التركيبات',
        'Technician/Workers' => 'فني / عامل',
        'Legal/Contracts' => 'قانون / عقد',
        'Chemistry/Laboratories' => 'كيمياء / مختبرات',
        'Logistics/Warehouse/Supply Chain' => 'الخدمات اللوجستية / مستودع / الموردين',
        'Sales/Retail/Distribution' => 'مبيعات / بيع بالتجزئة / توزيع',
        'Accounting/Finance' => 'محاسبة / تمويل',
        'Project/Program Management' => 'مشروع / إدارة البرنامج',
        'Purchasing/Procurement' => 'شراء / تحصيل',
        'Restaurant/Catering/Cuisine' => 'مطعم / تقديم الطعام / مطبخ',
        'Human Resources' => 'الموارد البشرية',
        'Fashion And Beauty' => 'الموضة والجمال',
        'Film And Photography/Sound/Music' => 'السينما والتصوير الفوتوغرافي / صوت / موسيقى',
        'Engineering - Construction/Civil/Architecture' => 'الهيئة الهندسية / مدني / هندسة معمارية',
        'Interior Design/Decoration' => 'تصميم داخلي / زخرفة',
        'Engineering - Other' => 'هندسة - اخرى',
        'Engineering - Telecom/Technology' => 'هندسة - اتصالات / اتصالات',
        'Engineering - Mechanical/Electrical/Medical' => 'الهندسة الميكانيكية / الكهرباء',
        'Engineering - Oil & Gas/Energy' => 'الهندسة - النفط والغاز / الطاقة',
        'C-Level Executive/GM/Director' => 'C-Level تنفيذي / GM / مخرج',
        'Psychological Support/Community Services' => 'دعم نفسي / خدمات المجتمع',
        'Other' => 'اخرى',
        ];
}
    public static function getExperienceTranslations()
{
    return [
        'None' => 'لا يوجد',
        '1 Year' => '1 سنة',
        '2 Years' => '2 سنة',
        '3 Years' => '3 سنوات',
        '4 Years' => '4 سنوات',
        '5 Years' => '5 سنوات',
        '6 Years' => '6 سنوات',
        '7 Years' => '7 سنوات',
        '8 Years' => '8 سنوات',
        '9 Years' => '9 سنوات',
        '10 Years' => '10 سنوات',
        'More Than 10 Years' => 'أكثر من 10 سنوات',
    ];
}

}
<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pages() as $slug => $page) {
            StaticPage::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => ['en' => $page['title']['en'], 'ar' => $page['title']['ar']],
                    'content' => ['en' => $page['content']['en'], 'ar' => $page['content']['ar']],
                    'is_active' => true,
                ]
            );
        }
    }

    private function section(int $num, string $id, string $title, string $body): string
    {
        return '<section id="'.$id.'"><h2><span class="web-policy-num">'.$num.'</span> '.$title.'</h2>'.$body.'</section>';
    }

    private function wrap(string $tocHeading, string $toc, string $content): string
    {
        return '<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">'
            .'<aside class="lg:col-span-3"><div class="web-policy-toc"><h3>'.$tocHeading.'</h3>'.$toc.'</div></aside>'
            .'<div class="lg:col-span-9">'.$content.'</div>'
            .'</div>';
    }

    private function pages(): array
    {
        return [
            'terms-conditions' => $this->terms(),
            'privacy-policy' => $this->privacy(),
            'refund-policy' => $this->refund(),
            'shipping-policy' => $this->shipping(),
        ];
    }

    private function terms(): array
    {
        $enToc = [
            ['acceptance', '1. Acceptance of Terms'],
            ['use-of-website', '2. Use of Our Website'],
            ['account', '3. Account Registration'],
            ['orders-payment', '4. Orders &amp; Payment'],
            ['pricing', '5. Pricing &amp; Product Information'],
            ['ip', '6. Intellectual Property'],
            ['liability', '7. Limitation of Liability'],
            ['governing-law', '8. Governing Law'],
            ['changes', '9. Changes to These Terms'],
            ['contact-us', '10. Contact Us'],
        ];
        $enTocHtml = collect($enToc)->map(fn ($t) => '<a href="#'.$t[0].'">'.$t[1].'</a>')->implode('');

        $enContent = $this->section(1, 'acceptance', 'Acceptance of Terms',
                '<p>These Terms &amp; Conditions ("Terms") govern your access to and use of the '.config('app.name').' website and services. By browsing our store, creating an account, or placing an order, you agree to these Terms in full. If you do not agree, please do not use our website.</p>')
            .$this->section(2, 'use-of-website', 'Use of Our Website',
                '<p>You agree to use our website only for lawful purposes and in a way that doesn\'t infringe the rights of, or restrict or inhibit the use of, this site by anyone else. Prohibited behavior includes attempting to gain unauthorized access to our systems, scraping content without permission, or using the site to distribute harmful software.</p>')
            .$this->section(3, 'account', 'Account Registration',
                '<p>To place orders and access certain features, you may need to create an account. You\'re responsible for maintaining the confidentiality of your login details and for all activity that occurs under your account. Please notify us immediately of any unauthorized use.</p>')
            .$this->section(4, 'orders-payment', 'Orders &amp; Payment',
                '<p>When you place an order, you\'re making an offer to purchase the selected products, which we may accept or decline at our discretion &mdash; for example, in cases of pricing errors or stock unavailability. Payment must be received in full before an order is processed, using one of our accepted payment methods.</p>'
                .'<div class="web-policy-callout"><i class="fa-solid fa-circle-info"></i><span>An order confirmation email does not guarantee acceptance of your order; it confirms that we\'ve received it.</span></div>')
            .$this->section(5, 'pricing', 'Pricing &amp; Product Information',
                '<p>We work hard to ensure prices, descriptions and images are accurate, but occasional errors may occur. If we discover a pricing or listing error after you\'ve placed an order, we\'ll contact you before processing, and you may choose to proceed at the correct price or cancel your order for a full refund.</p>')
            .$this->section(6, 'ip', 'Intellectual Property',
                '<p>All content on this website &mdash; including text, graphics, logos, product images and software &mdash; is the property of '.config('app.name').' or its licensors and is protected by copyright and trademark law. You may not reproduce, distribute, or create derivative works from our content without written permission.</p>')
            .$this->section(7, 'liability', 'Limitation of Liability',
                '<p>To the fullest extent permitted by law, '.config('app.name').' is not liable for any indirect, incidental, or consequential damages arising from your use of our website or products. Our total liability for any claim relating to your order will not exceed the amount you paid for that order.</p>')
            .$this->section(8, 'governing-law', 'Governing Law',
                '<p>These Terms are governed by the laws of the Arab Republic of Egypt, without regard to conflict of law principles. Any disputes arising from these Terms will be resolved in the courts located in Cairo, Egypt.</p>')
            .$this->section(9, 'changes', 'Changes to These Terms',
                '<p>We may revise these Terms from time to time. Changes take effect as soon as they\'re posted on this page, and your continued use of our website after changes are posted constitutes acceptance of the updated Terms.</p>')
            .$this->section(10, 'contact-us', 'Contact Us',
                '<p>If you have questions about these Terms &amp; Conditions, reach out to us anytime.</p>'
                .'<ul><li>Email: support@brownbox.test</li><li>Or visit our <a href="'.route('web.contact.index', ['lang' => 'en']).'" class="web-inline-link">Contact page</a></li></ul>');

        $arToc = [
            ['acceptance', '1. قبول الشروط'],
            ['use-of-website', '2. استخدام موقعنا'],
            ['account', '3. تسجيل الحساب'],
            ['orders-payment', '4. الطلبات والدفع'],
            ['pricing', '5. الأسعار ومعلومات المنتج'],
            ['ip', '6. الملكية الفكرية'],
            ['liability', '7. حدود المسؤولية'],
            ['governing-law', '8. القانون الحاكم'],
            ['changes', '9. التغييرات على هذه الشروط'],
            ['contact-us', '10. تواصل معنا'],
        ];
        $arTocHtml = collect($arToc)->map(fn ($t) => '<a href="#'.$t[0].'">'.$t[1].'</a>')->implode('');

        $arContent = $this->section(1, 'acceptance', 'قبول الشروط',
                '<p>تحكم هذه الشروط والأحكام ("الشروط") وصولك إلى موقع وخدمات '.config('app.name').' واستخدامك لها. من خلال تصفح متجرنا، أو إنشاء حساب، أو تقديم طلب، فإنك توافق على هذه الشروط بالكامل. إذا كنت لا توافق، يرجى عدم استخدام موقعنا.</p>')
            .$this->section(2, 'use-of-website', 'استخدام موقعنا',
                '<p>توافق على استخدام موقعنا لأغراض مشروعة فقط وبطريقة لا تنتهك حقوق الآخرين أو تقيّد استخدامهم لهذا الموقع. يشمل السلوك المحظور محاولة الوصول غير المصرح به إلى أنظمتنا، أو استخراج المحتوى دون إذن، أو استخدام الموقع لنشر برمجيات ضارة.</p>')
            .$this->section(3, 'account', 'تسجيل الحساب',
                '<p>لتقديم الطلبات والوصول إلى بعض الميزات، قد تحتاج إلى إنشاء حساب. أنت مسؤول عن الحفاظ على سرية بيانات الدخول الخاصة بك وعن جميع الأنشطة التي تتم من خلال حسابك. يرجى إبلاغنا فورًا بأي استخدام غير مصرح به.</p>')
            .$this->section(4, 'orders-payment', 'الطلبات والدفع',
                '<p>عند تقديم طلب، فأنت تقدم عرضًا لشراء المنتجات المختارة، والذي يجوز لنا قبوله أو رفضه وفق تقديرنا &mdash; على سبيل المثال في حالات أخطاء التسعير أو نفاد المخزون. يجب استلام الدفع بالكامل قبل معالجة الطلب، باستخدام إحدى وسائل الدفع المعتمدة لدينا.</p>'
                .'<div class="web-policy-callout"><i class="fa-solid fa-circle-info"></i><span>لا يضمن بريد تأكيد الطلب قبول طلبك؛ فهو يؤكد فقط أننا استلمناه.</span></div>')
            .$this->section(5, 'pricing', 'الأسعار ومعلومات المنتج',
                '<p>نعمل جاهدين للتأكد من دقة الأسعار والأوصاف والصور، لكن قد تحدث أخطاء أحيانًا. إذا اكتشفنا خطأ في السعر أو بيانات المنتج بعد تقديم طلبك، سنتواصل معك قبل المعالجة، ويمكنك اختيار المتابعة بالسعر الصحيح أو إلغاء طلبك واسترداد كامل المبلغ.</p>')
            .$this->section(6, 'ip', 'الملكية الفكرية',
                '<p>جميع المحتويات الموجودة على هذا الموقع &mdash; بما في ذلك النصوص والرسومات والشعارات وصور المنتجات والبرمجيات &mdash; هي ملك لـ'.config('app.name').' أو للجهات المرخصة لها، ومحمية بموجب قوانين حقوق النشر والعلامات التجارية. لا يجوز لك نسخ أو توزيع أو إنشاء أعمال مشتقة من محتوانا دون إذن كتابي.</p>')
            .$this->section(7, 'liability', 'حدود المسؤولية',
                '<p>إلى أقصى حد يسمح به القانون، لا تتحمل '.config('app.name').' مسؤولية أي أضرار غير مباشرة أو عرضية أو تبعية ناتجة عن استخدامك لموقعنا أو منتجاتنا. لن تتجاوز مسؤوليتنا الإجمالية عن أي مطالبة متعلقة بطلبك المبلغ الذي دفعته مقابل هذا الطلب.</p>')
            .$this->section(8, 'governing-law', 'القانون الحاكم',
                '<p>تخضع هذه الشروط لقوانين جمهورية مصر العربية، بغض النظر عن مبادئ تنازع القوانين. تتم تسوية أي نزاعات ناشئة عن هذه الشروط في محاكم مدينة القاهرة، مصر.</p>')
            .$this->section(9, 'changes', 'التغييرات على هذه الشروط',
                '<p>قد نقوم بمراجعة هذه الشروط من وقت لآخر. تصبح التغييرات سارية المفعول فور نشرها على هذه الصفحة، ويُعد استمرارك في استخدام موقعنا بعد نشر التغييرات موافقة على الشروط المحدثة.</p>')
            .$this->section(10, 'contact-us', 'تواصل معنا',
                '<p>إذا كانت لديك أسئلة حول هذه الشروط والأحكام، تواصل معنا في أي وقت.</p>'
                .'<ul><li>البريد الإلكتروني: support@brownbox.test</li><li>أو زر <a href="'.route('web.contact.index', ['lang' => 'ar']).'" class="web-inline-link">صفحة التواصل</a></li></ul>');

        return [
            'title' => ['en' => 'Terms & Conditions', 'ar' => 'الشروط والأحكام'],
            'content' => [
                'en' => $this->wrap('On This Page', $enTocHtml, $enContent),
                'ar' => $this->wrap('في هذه الصفحة', $arTocHtml, $arContent),
            ],
        ];
    }

    private function privacy(): array
    {
        $enToc = [
            ['information-we-collect', '1. Information We Collect'],
            ['how-we-use', '2. How We Use Your Information'],
            ['cookies', '3. Cookies &amp; Tracking'],
            ['sharing', '4. Information Sharing'],
            ['security', '5. Data Security'],
            ['your-rights', '6. Your Rights &amp; Choices'],
            ['children', '7. Children\'s Privacy'],
            ['changes', '8. Changes to This Policy'],
            ['contact-us', '9. Contact Us'],
        ];
        $enTocHtml = collect($enToc)->map(fn ($t) => '<a href="#'.$t[0].'">'.$t[1].'</a>')->implode('');

        $enContent = $this->section(1, 'information-we-collect', 'Information We Collect',
                '<p>We collect information you provide directly to us, such as when you create an account, place an order, subscribe to our newsletter, or contact customer support. This may include your name, email address, phone number, shipping and billing address, and payment details.</p>'
                .'<p>We also automatically collect certain information when you use our site, including your IP address, browser type, device information, pages visited, and referring URLs, in order to keep the store running smoothly and secure.</p>')
            .$this->section(2, 'how-we-use', 'How We Use Your Information',
                '<p>We use the information we collect to:</p>'
                .'<ul><li>Process and fulfill your orders, including shipping and payment confirmation</li><li>Communicate with you about your orders, account, and customer support requests</li><li>Send you marketing communications, where you\'ve opted in, which you can unsubscribe from at any time</li><li>Improve our website, products, and overall shopping experience</li><li>Detect, investigate, and prevent fraudulent transactions and other illegal activity</li></ul>')
            .$this->section(3, 'cookies', 'Cookies &amp; Tracking',
                '<p>Like most online stores, we use cookies and similar tracking technologies to keep you signed in, remember items in your cart, and understand how our site is used so we can improve it over time.</p>'
                .'<div class="web-policy-callout"><i class="fa-solid fa-circle-info"></i><span>You can control cookies through your browser settings. Disabling cookies may affect how parts of the site function, such as your cart or saved preferences.</span></div>')
            .$this->section(4, 'sharing', 'Information Sharing',
                '<p>We do not sell your personal information. We may share information with trusted third parties who help us operate our business, such as payment processors, shipping carriers, and analytics providers &mdash; each of which is required to protect your data and use it only for the services they provide us.</p>'
                .'<p>We may also disclose information if required by law, or to protect the rights, property, or safety of '.config('app.name').', our customers, or others.</p>')
            .$this->section(5, 'security', 'Data Security',
                '<p>We use industry-standard security measures, including encryption in transit and restricted access controls, to protect your personal information. While no method of transmission over the internet is 100% secure, we work continuously to safeguard your data against unauthorized access, alteration, or disclosure.</p>')
            .$this->section(6, 'your-rights', 'Your Rights &amp; Choices',
                '<p>Depending on your location, you may have the right to access, correct, delete, or export the personal information we hold about you, and to opt out of marketing communications at any time.</p>'
                .'<ul><li>Update your account details anytime from your <a href="'.route('web.account.index', ['lang' => 'en']).'" class="web-inline-link">Account</a> page</li><li>Unsubscribe from marketing emails using the link at the bottom of any email</li><li>Contact us to request a copy or deletion of your data</li></ul>')
            .$this->section(7, 'children', 'Children\'s Privacy',
                '<p>Our services are not directed to children under 16, and we do not knowingly collect personal information from children. If you believe a child has provided us with personal information, please contact us so we can remove it.</p>')
            .$this->section(8, 'changes', 'Changes to This Policy',
                '<p>We may update this Privacy Policy from time to time to reflect changes in our practices or for legal, operational, or regulatory reasons. We\'ll post the updated policy here with a revised "Last updated" date, and encourage you to review it periodically.</p>')
            .$this->section(9, 'contact-us', 'Contact Us',
                '<p>If you have any questions about this Privacy Policy or how we handle your information, reach out to our team &mdash; we\'re happy to help.</p>'
                .'<ul><li>Email: support@brownbox.test</li><li>Or visit our <a href="'.route('web.contact.index', ['lang' => 'en']).'" class="web-inline-link">Contact page</a></li></ul>');

        $arToc = [
            ['information-we-collect', '1. المعلومات التي نجمعها'],
            ['how-we-use', '2. كيف نستخدم معلوماتك'],
            ['cookies', '3. ملفات تعريف الارتباط والتتبع'],
            ['sharing', '4. مشاركة المعلومات'],
            ['security', '5. أمان البيانات'],
            ['your-rights', '6. حقوقك وخياراتك'],
            ['children', '7. خصوصية الأطفال'],
            ['changes', '8. التغييرات على هذه السياسة'],
            ['contact-us', '9. تواصل معنا'],
        ];
        $arTocHtml = collect($arToc)->map(fn ($t) => '<a href="#'.$t[0].'">'.$t[1].'</a>')->implode('');

        $arContent = $this->section(1, 'information-we-collect', 'المعلومات التي نجمعها',
                '<p>نجمع المعلومات التي تقدمها لنا مباشرة، مثل عند إنشاء حساب، أو تقديم طلب، أو الاشتراك في نشرتنا البريدية، أو التواصل مع خدمة العملاء. قد يشمل ذلك اسمك وبريدك الإلكتروني ورقم هاتفك وعنوان الشحن والفوترة وبيانات الدفع.</p>'
                .'<p>كما نقوم تلقائيًا بجمع بعض المعلومات عند استخدامك لموقعنا، مثل عنوان IP ونوع المتصفح ومعلومات الجهاز والصفحات التي تمت زيارتها، وذلك للحفاظ على تشغيل المتجر بسلاسة وأمان.</p>')
            .$this->section(2, 'how-we-use', 'كيف نستخدم معلوماتك',
                '<p>نستخدم المعلومات التي نجمعها من أجل:</p>'
                .'<ul><li>معالجة طلباتك وإتمامها، بما في ذلك تأكيد الشحن والدفع</li><li>التواصل معك بخصوص طلباتك وحسابك وطلبات الدعم</li><li>إرسال رسائل تسويقية عند موافقتك، مع إمكانية إلغاء الاشتراك في أي وقت</li><li>تحسين موقعنا ومنتجاتنا وتجربة التسوق بشكل عام</li><li>اكتشاف ومنع المعاملات الاحتيالية والأنشطة غير القانونية</li></ul>')
            .$this->section(3, 'cookies', 'ملفات تعريف الارتباط والتتبع',
                '<p>مثل معظم المتاجر الإلكترونية، نستخدم ملفات تعريف الارتباط وتقنيات التتبع المشابهة لإبقائك مسجلاً للدخول، وتذكر عناصر سلة التسوق، وفهم كيفية استخدام موقعنا لتحسينه بمرور الوقت.</p>'
                .'<div class="web-policy-callout"><i class="fa-solid fa-circle-info"></i><span>يمكنك التحكم في ملفات تعريف الارتباط من خلال إعدادات متصفحك. قد يؤثر تعطيلها على بعض وظائف الموقع، مثل سلة التسوق أو التفضيلات المحفوظة.</span></div>')
            .$this->section(4, 'sharing', 'مشاركة المعلومات',
                '<p>نحن لا نبيع معلوماتك الشخصية. قد نشارك المعلومات مع أطراف ثالثة موثوقة تساعدنا في تشغيل أعمالنا، مثل معالجات الدفع وشركات الشحن ومزودي التحليلات &mdash; وكل منها ملزم بحماية بياناتك واستخدامها فقط للخدمات التي يقدمونها لنا.</p>'
                .'<p>قد نكشف أيضًا عن المعلومات إذا تطلب القانون ذلك، أو لحماية حقوق أو ممتلكات أو سلامة '.config('app.name').' أو عملائنا أو الآخرين.</p>')
            .$this->section(5, 'security', 'أمان البيانات',
                '<p>نستخدم إجراءات أمنية معيارية في الصناعة، بما في ذلك التشفير أثناء النقل وضوابط الوصول المقيدة، لحماية معلوماتك الشخصية. وعلى الرغم من عدم وجود طريقة نقل عبر الإنترنت آمنة بنسبة 100%، إلا أننا نعمل باستمرار على حماية بياناتك من الوصول أو التعديل أو الإفصاح غير المصرح به.</p>')
            .$this->section(6, 'your-rights', 'حقوقك وخياراتك',
                '<p>بحسب موقعك، قد يكون لديك الحق في الوصول إلى معلوماتك الشخصية أو تصحيحها أو حذفها أو تصديرها، وإلغاء الاشتراك في الرسائل التسويقية في أي وقت.</p>'
                .'<ul><li>تحديث بيانات حسابك في أي وقت من صفحة <a href="'.route('web.account.index', ['lang' => 'ar']).'" class="web-inline-link">حسابي</a></li><li>إلغاء الاشتراك في الرسائل التسويقية عبر الرابط الموجود أسفل أي بريد إلكتروني</li><li>التواصل معنا لطلب نسخة من بياناتك أو حذفها</li></ul>')
            .$this->section(7, 'children', 'خصوصية الأطفال',
                '<p>خدماتنا غير موجهة للأطفال دون سن 16 عامًا، ونحن لا نجمع عن قصد معلومات شخصية من الأطفال. إذا كنت تعتقد أن طفلاً قدم لنا معلومات شخصية، يرجى التواصل معنا لإزالتها.</p>')
            .$this->section(8, 'changes', 'التغييرات على هذه السياسة',
                '<p>قد نقوم بتحديث سياسة الخصوصية هذه من وقت لآخر لتعكس التغييرات في ممارساتنا أو لأسباب قانونية أو تشغيلية أو تنظيمية. سننشر السياسة المحدثة هنا مع تاريخ "آخر تحديث" جديد، ونشجعك على مراجعتها بشكل دوري.</p>')
            .$this->section(9, 'contact-us', 'تواصل معنا',
                '<p>إذا كانت لديك أي أسئلة حول سياسة الخصوصية هذه أو كيفية تعاملنا مع معلوماتك، تواصل مع فريقنا &mdash; يسعدنا مساعدتك.</p>'
                .'<ul><li>البريد الإلكتروني: support@brownbox.test</li><li>أو زر <a href="'.route('web.contact.index', ['lang' => 'ar']).'" class="web-inline-link">صفحة التواصل</a></li></ul>');

        return [
            'title' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'],
            'content' => [
                'en' => $this->wrap('On This Page', $enTocHtml, $enContent),
                'ar' => $this->wrap('في هذه الصفحة', $arTocHtml, $arContent),
            ],
        ];
    }

    private function refund(): array
    {
        $enToc = [
            ['eligibility', '1. Return Eligibility'],
            ['return-window', '2. Return Window'],
            ['how-to-return', '3. How to Start a Return'],
            ['refund-process', '4. Refund Process &amp; Timeline'],
            ['non-returnable', '5. Non-Returnable Items'],
            ['exchanges', '6. Exchanges'],
            ['damaged', '7. Damaged or Defective Items'],
            ['contact-us', '8. Contact Us'],
        ];
        $enTocHtml = collect($enToc)->map(fn ($t) => '<a href="#'.$t[0].'">'.$t[1].'</a>')->implode('');

        $enContent = $this->section(1, 'eligibility', 'Return Eligibility',
                '<p>We want you to love what you ordered. If something isn\'t right, most items can be returned as long as they are unused, in their original packaging, and in the same condition you received them, with all tags and accessories included.</p>')
            .$this->section(2, 'return-window', 'Return Window',
                '<p>You have <strong>30 days</strong> from the delivery date to start a return for most items. Items marked as final sale, or purchased during clearance events, are not eligible for return unless defective.</p>')
            .$this->section(3, 'how-to-return', 'How to Start a Return',
                '<ol><li>Go to <a href="'.route('web.account.orders', ['lang' => 'en']).'" class="web-inline-link">My Orders</a> and select the order containing the item you\'d like to return</li><li>Choose "Request Return" and select a reason for the return</li><li>Print the prepaid return label we email you, and pack the item securely</li><li>Drop the package off at any authorized carrier location</li></ol>'
                .'<p>Don\'t have an account, or need help? Our <a href="'.route('web.contact.index', ['lang' => 'en']).'" class="web-inline-link">support team</a> can start the return for you.</p>')
            .$this->section(4, 'refund-process', 'Refund Process &amp; Timeline',
                '<p>Once we receive and inspect your returned item, we\'ll notify you by email and process your refund to the original payment method. Refunds typically take <strong>3&ndash;5 business days</strong> to appear, depending on your bank or card provider.</p>'
                .'<div class="web-policy-callout"><i class="fa-solid fa-circle-info"></i><span>Orders paid by bank transfer, Vodafone Cash or InstaPay are refunded to the same account or wallet used at checkout.</span></div>')
            .$this->section(5, 'non-returnable', 'Non-Returnable Items',
                '<p>For hygiene and safety reasons, the following items cannot be returned unless they arrive damaged or defective:</p>'
                .'<ul><li>Personal care and grooming products that have been opened</li><li>Underwear, swimwear, and earrings</li><li>Gift cards and digital products</li><li>Items marked "Final Sale" at checkout</li></ul>')
            .$this->section(6, 'exchanges', 'Exchanges',
                '<p>Need a different size or color instead? The fastest way is to return your original item for a refund and place a new order &mdash; this guarantees the replacement is in stock and ships right away.</p>')
            .$this->section(7, 'damaged', 'Damaged or Defective Items',
                '<p>If your item arrives damaged or defective, please contact us within 7 days of delivery with a photo of the issue. We\'ll arrange a free replacement or full refund, including any return shipping costs.</p>')
            .$this->section(8, 'contact-us', 'Contact Us',
                '<p>Questions about a return or refund in progress? We\'re here to help.</p>'
                .'<ul><li>Email: support@brownbox.test</li><li>Or visit our <a href="'.route('web.contact.index', ['lang' => 'en']).'" class="web-inline-link">Contact page</a></li></ul>');

        $arToc = [
            ['eligibility', '1. أهلية الإرجاع'],
            ['return-window', '2. مدة الإرجاع'],
            ['how-to-return', '3. كيفية بدء عملية إرجاع'],
            ['refund-process', '4. عملية الاسترداد والمدة الزمنية'],
            ['non-returnable', '5. المنتجات غير القابلة للإرجاع'],
            ['exchanges', '6. الاستبدال'],
            ['damaged', '7. المنتجات التالفة أو المعيبة'],
            ['contact-us', '8. تواصل معنا'],
        ];
        $arTocHtml = collect($arToc)->map(fn ($t) => '<a href="#'.$t[0].'">'.$t[1].'</a>')->implode('');

        $arContent = $this->section(1, 'eligibility', 'أهلية الإرجاع',
                '<p>نريدك أن تحب ما طلبته. إذا لم يكن الأمر مناسبًا، يمكن إرجاع معظم المنتجات طالما أنها غير مستخدمة، وفي عبوتها الأصلية، وبنفس الحالة التي استلمتها بها، مع جميع البطاقات والملحقات.</p>')
            .$this->section(2, 'return-window', 'مدة الإرجاع',
                '<p>لديك <strong>30 يومًا</strong> من تاريخ التسليم لبدء عملية إرجاع لمعظم المنتجات. المنتجات المحددة كـ"تخفيضات نهائية" أو التي تم شراؤها خلال عروض التصفية غير مؤهلة للإرجاع إلا إذا كانت معيبة.</p>')
            .$this->section(3, 'how-to-return', 'كيفية بدء عملية إرجاع',
                '<ol><li>اذهب إلى <a href="'.route('web.account.orders', ['lang' => 'ar']).'" class="web-inline-link">طلباتي</a> واختر الطلب الذي يحتوي على المنتج الذي ترغب في إرجاعه</li><li>اختر "طلب إرجاع" وحدد سبب الإرجاع</li><li>اطبع ملصق الإرجاع المدفوع مسبقًا الذي أرسلناه لك عبر البريد، وقم بتغليف المنتج بإحكام</li><li>سلّم الطرد في أقرب نقطة استلام معتمدة</li></ol>'
                .'<p>ليس لديك حساب أو تحتاج مساعدة؟ يمكن لفريق <a href="'.route('web.contact.index', ['lang' => 'ar']).'" class="web-inline-link">الدعم</a> بدء الإرجاع نيابة عنك.</p>')
            .$this->section(4, 'refund-process', 'عملية الاسترداد والمدة الزمنية',
                '<p>بمجرد استلامنا وفحصنا للمنتج المرتجع، سنُعلمك عبر البريد الإلكتروني ونعالج استرداد المبلغ إلى وسيلة الدفع الأصلية. عادة ما تستغرق عمليات الاسترداد من <strong>3 إلى 5 أيام عمل</strong> لتظهر، حسب البنك أو مزود البطاقة.</p>'
                .'<div class="web-policy-callout"><i class="fa-solid fa-circle-info"></i><span>الطلبات المدفوعة عبر التحويل البنكي أو فودافون كاش أو إنستاباي يتم استردادها إلى نفس الحساب أو المحفظة المستخدمة عند الدفع.</span></div>')
            .$this->section(5, 'non-returnable', 'المنتجات غير القابلة للإرجاع',
                '<p>لأسباب تتعلق بالنظافة والسلامة، لا يمكن إرجاع المنتجات التالية إلا إذا وصلت تالفة أو معيبة:</p>'
                .'<ul><li>منتجات العناية الشخصية التي تم فتحها</li><li>الملابس الداخلية وملابس السباحة والأقراط</li><li>بطاقات الهدايا والمنتجات الرقمية</li><li>المنتجات المحددة كـ"تخفيضات نهائية" عند الدفع</li></ul>')
            .$this->section(6, 'exchanges', 'الاستبدال',
                '<p>تحتاج مقاسًا أو لونًا مختلفًا؟ أسرع طريقة هي إرجاع المنتج الأصلي لاسترداد المبلغ وتقديم طلب جديد &mdash; مما يضمن توفر البديل وشحنه فورًا.</p>')
            .$this->section(7, 'damaged', 'المنتجات التالفة أو المعيبة',
                '<p>إذا وصل منتجك تالفًا أو معيبًا، يرجى التواصل معنا خلال 7 أيام من التسليم مع صورة توضح المشكلة. سنقوم بترتيب استبدال مجاني أو استرداد كامل للمبلغ، بما في ذلك تكاليف شحن الإرجاع.</p>')
            .$this->section(8, 'contact-us', 'تواصل معنا',
                '<p>لديك أسئلة حول عملية إرجاع أو استرداد جارية؟ نحن هنا للمساعدة.</p>'
                .'<ul><li>البريد الإلكتروني: support@brownbox.test</li><li>أو زر <a href="'.route('web.contact.index', ['lang' => 'ar']).'" class="web-inline-link">صفحة التواصل</a></li></ul>');

        return [
            'title' => ['en' => 'Returns & Refunds Policy', 'ar' => 'سياسة الإرجاع والاسترداد'],
            'content' => [
                'en' => $this->wrap('On This Page', $enTocHtml, $enContent),
                'ar' => $this->wrap('في هذه الصفحة', $arTocHtml, $arContent),
            ],
        ];
    }

    private function shipping(): array
    {
        $enToc = [
            ['processing-time', '1. Processing Time'],
            ['shipping-methods', '2. Shipping Methods &amp; Rates'],
            ['delivery-estimates', '3. Delivery Estimates'],
            ['international', '4. International Shipping'],
            ['order-tracking', '5. Order Tracking'],
            ['shipping-delays', '6. Shipping Delays'],
            ['damaged-lost', '7. Damaged or Lost Packages'],
            ['contact-us', '8. Contact Us'],
        ];
        $enTocHtml = collect($enToc)->map(fn ($t) => '<a href="#'.$t[0].'">'.$t[1].'</a>')->implode('');

        $enContent = $this->section(1, 'processing-time', 'Processing Time',
                '<p>Orders are typically processed and prepared for shipment within <strong>1&ndash;2 business days</strong> of purchase. Orders placed on weekends or public holidays are processed on the next business day. During high-demand periods, such as flash sales, processing may take slightly longer.</p>'
                .'<p>You\'ll receive an email confirmation once your order has been placed, and a second email with tracking details once it ships.</p>')
            .$this->section(2, 'shipping-methods', 'Shipping Methods &amp; Rates',
                '<p>We offer several shipping options at checkout, so you can choose the balance of speed and cost that works best for you.</p>'
                .'<table class="web-policy-table"><thead><tr><th>Method</th><th>Estimated Delivery</th><th>Cost</th></tr></thead><tbody>'
                .'<tr><td>Standard Shipping</td><td>4&ndash;7 business days</td><td>Free over EGP 1,500, otherwise EGP 60</td></tr>'
                .'<tr><td>Express Shipping</td><td>2&ndash;3 business days</td><td>EGP 120</td></tr>'
                .'<tr><td>Next-Day Shipping</td><td>1 business day</td><td>EGP 200</td></tr>'
                .'</tbody></table>'
                .'<p>Shipping costs and available methods are calculated and confirmed at checkout based on your delivery address and cart contents.</p>')
            .$this->section(3, 'delivery-estimates', 'Delivery Estimates',
                '<p>Delivery estimates are calculated from the day your order ships, not the day it\'s placed, and are business-day estimates rather than guarantees. Rural or remote addresses may experience slightly longer transit times.</p>')
            .$this->section(4, 'international', 'International Shipping',
                '<p>We currently ship to select international destinations. International orders may be subject to customs duties, taxes, and import fees, which are the responsibility of the recipient and are not included in our shipping charges. Delivery times for international orders typically range from 7&ndash;21 business days depending on destination and customs processing.</p>')
            .$this->section(5, 'order-tracking', 'Order Tracking',
                '<p>Once your order ships, you\'ll receive a tracking number by email. You can also check the latest status anytime from your <a href="'.route('web.account.orders', ['lang' => 'en']).'" class="web-inline-link">My Orders</a> page or our dedicated <a href="'.route('web.track-order.index', ['lang' => 'en']).'" class="web-inline-link">Track Order</a> page.</p>')
            .$this->section(6, 'shipping-delays', 'Shipping Delays',
                '<p>While we work with reliable carriers, delivery can occasionally be delayed by factors outside our control, including weather, customs processing, or high shipping volumes during peak seasons. We\'ll do our best to keep you informed if a known delay affects your order.</p>'
                .'<div class="web-policy-callout"><i class="fa-solid fa-circle-info"></i><span>If your order hasn\'t arrived within the estimated window, please contact us and we\'ll investigate right away.</span></div>')
            .$this->section(7, 'damaged-lost', 'Damaged or Lost Packages',
                '<p>If your package arrives damaged, or tracking shows it as delivered but you haven\'t received it, please contact our support team within 7 days so we can file a claim with the carrier and arrange a replacement or refund.</p>')
            .$this->section(8, 'contact-us', 'Contact Us',
                '<p>Have a question about a specific order or our shipping options in general? We\'re happy to help.</p>'
                .'<ul><li>Email: support@brownbox.test</li><li>Or visit our <a href="'.route('web.contact.index', ['lang' => 'en']).'" class="web-inline-link">Contact page</a></li></ul>');

        $arToc = [
            ['processing-time', '1. مدة التجهيز'],
            ['shipping-methods', '2. طرق وأسعار الشحن'],
            ['delivery-estimates', '3. مواعيد التسليم المتوقعة'],
            ['international', '4. الشحن الدولي'],
            ['order-tracking', '5. تتبع الطلب'],
            ['shipping-delays', '6. تأخيرات الشحن'],
            ['damaged-lost', '7. الطرود التالفة أو المفقودة'],
            ['contact-us', '8. تواصل معنا'],
        ];
        $arTocHtml = collect($arToc)->map(fn ($t) => '<a href="#'.$t[0].'">'.$t[1].'</a>')->implode('');

        $arContent = $this->section(1, 'processing-time', 'مدة التجهيز',
                '<p>عادةً ما تتم معالجة الطلبات وتجهيزها للشحن خلال <strong>1 إلى 2 يوم عمل</strong> من الشراء. تتم معالجة الطلبات المقدمة في عطلات نهاية الأسبوع أو الإجازات الرسمية في يوم العمل التالي. خلال فترات الطلب المرتفع، مثل التخفيضات السريعة، قد تستغرق المعالجة وقتًا أطول قليلاً.</p>'
                .'<p>ستتلقى رسالة تأكيد بالبريد الإلكتروني بمجرد تقديم طلبك، ورسالة ثانية تحتوي على تفاصيل التتبع بمجرد شحنه.</p>')
            .$this->section(2, 'shipping-methods', 'طرق وأسعار الشحن',
                '<p>نقدم عدة خيارات للشحن عند الدفع، بحيث يمكنك اختيار التوازن المناسب بين السرعة والتكلفة.</p>'
                .'<table class="web-policy-table"><thead><tr><th>الطريقة</th><th>مدة التسليم المتوقعة</th><th>التكلفة</th></tr></thead><tbody>'
                .'<tr><td>الشحن العادي</td><td>4 إلى 7 أيام عمل</td><td>مجاني فوق 1,500 جنيه، وإلا 60 جنيهًا</td></tr>'
                .'<tr><td>الشحن السريع</td><td>2 إلى 3 أيام عمل</td><td>120 جنيهًا</td></tr>'
                .'<tr><td>الشحن في نفس اليوم التالي</td><td>يوم عمل واحد</td><td>200 جنيه</td></tr>'
                .'</tbody></table>'
                .'<p>يتم حساب تكاليف الشحن والطرق المتاحة وتأكيدها عند الدفع بناءً على عنوان التسليم ومحتويات سلة التسوق.</p>')
            .$this->section(3, 'delivery-estimates', 'مواعيد التسليم المتوقعة',
                '<p>يتم حساب مواعيد التسليم المتوقعة من يوم شحن طلبك، وليس يوم تقديمه، وهي تقديرات بأيام العمل وليست ضمانات. قد تشهد العناوين الريفية أو النائية وقت وصول أطول قليلاً.</p>')
            .$this->section(4, 'international', 'الشحن الدولي',
                '<p>نقوم حاليًا بالشحن إلى وجهات دولية محددة. قد تخضع الطلبات الدولية لرسوم جمركية وضرائب ورسوم استيراد، وهي مسؤولية المستلم ولا تشمل رسوم الشحن الخاصة بنا. تتراوح مدة التسليم للطلبات الدولية عادةً بين 7 و21 يوم عمل حسب الوجهة وإجراءات الجمارك.</p>')
            .$this->section(5, 'order-tracking', 'تتبع الطلب',
                '<p>بمجرد شحن طلبك، ستتلقى رقم تتبع عبر البريد الإلكتروني. يمكنك أيضًا التحقق من أحدث حالة في أي وقت من صفحة <a href="'.route('web.account.orders', ['lang' => 'ar']).'" class="web-inline-link">طلباتي</a> أو صفحة <a href="'.route('web.track-order.index', ['lang' => 'ar']).'" class="web-inline-link">تتبع الطلب</a> المخصصة.</p>')
            .$this->section(6, 'shipping-delays', 'تأخيرات الشحن',
                '<p>على الرغم من تعاملنا مع شركات شحن موثوقة، قد يتأخر التسليم أحيانًا بسبب عوامل خارجة عن إرادتنا، مثل الطقس أو إجراءات الجمارك أو ارتفاع أحجام الشحن خلال المواسم المزدحمة. سنبذل قصارى جهدنا لإبقائك على اطلاع إذا أثّر تأخير معروف على طلبك.</p>'
                .'<div class="web-policy-callout"><i class="fa-solid fa-circle-info"></i><span>إذا لم يصل طلبك خلال المدة المتوقعة، يرجى التواصل معنا وسنقوم بالتحقيق فورًا.</span></div>')
            .$this->section(7, 'damaged-lost', 'الطرود التالفة أو المفقودة',
                '<p>إذا وصل طردك تالفًا، أو أظهر التتبع أنه تم التسليم لكنك لم تستلمه، يرجى التواصل مع فريق الدعم خلال 7 أيام حتى نتمكن من تقديم شكوى لشركة الشحن وترتيب استبدال أو استرداد.</p>')
            .$this->section(8, 'contact-us', 'تواصل معنا',
                '<p>لديك سؤال حول طلب معين أو خيارات الشحن بشكل عام؟ يسعدنا مساعدتك.</p>'
                .'<ul><li>البريد الإلكتروني: support@brownbox.test</li><li>أو زر <a href="'.route('web.contact.index', ['lang' => 'ar']).'" class="web-inline-link">صفحة التواصل</a></li></ul>');

        return [
            'title' => ['en' => 'Shipping Policy', 'ar' => 'سياسة الشحن'],
            'content' => [
                'en' => $this->wrap('On This Page', $enTocHtml, $enContent),
                'ar' => $this->wrap('في هذه الصفحة', $arTocHtml, $arContent),
            ],
        ];
    }
}

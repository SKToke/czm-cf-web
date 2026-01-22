<?php

namespace App\Http\Controllers\Api;

use App\Enums\PublicationTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\AppQuranicVerse;
use App\Models\Banner;
use App\Models\Content;
use App\Models\CzmSupportCounter;
use App\Models\Publication;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    use HttpResponses;

    private function formatCounterValue(int $value): string
    {
        if ($value >= 1000000000) {
            return round($value / 1000000000, 1) . 'B';
        } elseif ($value >= 1000000) {
            return round($value / 1000000, 1) . 'M';
        } elseif ($value >= 1000) {
            return round($value / 1000, 1) . 'k';
        }
        return (string)$value;
    }

    public function achievements(): JsonResponse
    {
        try {
            $counters = CzmSupportCounter::getAllCounters();

            foreach ($counters as &$counter) {
                $counter['value'] = $this->formatCounterValue($counter['value']);
                unset($counter['icon']);
            }

            $data = [
                'total_counters' => count($counters),
                'counters' => $counters
            ];

            return $this->success('Achievements', $data);

        } catch (ModelNotFoundException $e) {
            return $this->error(
                'Failed to fetch the data',
                [
                    'message' => $e->getMessage()
                ],
                401
            );

        } catch (\Exception $e) {
            return $this->error(
                'Server error',
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }

    public function appQuranicVerse()
    {
        try {
            $verses = AppQuranicVerse::all();

            foreach ($verses as &$verse) {
                $verse['verse_text'] = $verse['quranic_verse_text'];
                unset($verse['created_at']);
                unset($verse['updated_at']);
                unset($verse['quranic_verse_text']);
            }

            $data = [
                'total_verses' => count($verses),
                'verses' => $verses
            ];

            return $this->success('Quranic Verses for App', $data);

        } catch (ModelNotFoundException $e) {
            return $this->error(
                'Failed to fetch the data',
                [
                    'message' => $e->getMessage()
                ],
                401
            );

        } catch (\Exception $e) {
            return $this->error(
                'Server error',
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }

    public function getCategories(): JsonResponse
    {
        try {
            $defaultCategories = collect([
                ['slug' => 'emergency', 'title' => 'Emergency']
            ]);
            $categories = Category::whereNull('program_id')
                ->get(['slug','title']);

            $allCategories = $defaultCategories->merge($categories);

            $data = [
                'total_categories' => $allCategories->count(),
                'categories' => $allCategories
            ];

            return $this->success('Categories', $data);

        } catch (\Exception $e) {
            return $this->error(
                'Server error',
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }

    public function zakatInQuran()
    {
        try {
            $banner = Banner::getBannerFor('Zakat in Quran');
            $content = Content::quranicverse()->first();
            if($content){
                $contentSections = $content->contentSections()->orderBy('position')->get();
            }

            foreach ($contentSections as &$section) {
                unset($section['deleted_at']);
                unset($section['created_at']);
                unset($section['updated_at']);
                unset($section['content_id']);
                if ($section['image']) {
                    $section['image'] = asset($this->getDynamicImageUrl($section['image']));
                }
            }

            $data = [
                'banner' => $banner->getImageUrl(),
                'total_sections' => count($contentSections),
                'content_sections' => $contentSections
            ];

            return $this->success('Zakat In Quran Page Content', $data);

        } catch (\Exception $e) {
            return $this->error(
                'Server error',
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }

    public function zakatInHadith()
    {
        try {
            $banner = Banner::getBannerFor('Zakat in Hadith');

            $sections = [
                [
                    "title" => "Abu Hurairah (May Allah be pleased with him) reported",
                    "body" => "A bedouin came to the Prophet (ﷺ) and said: “O Messenger of Allah! Direct me to a deed by which I may be entitled to enter Jannah.” The Prophet (ﷺ) said, “Worship Allah, and never associate anything with Him, establish Salat, pay the Zakat which has been enjoined upon you, and observe Saum of Ramadan.” He (the bedouin) said: “By Him in Whose Hand my soul is, I will never add anything to these (obligations).” When he turned his back, the Prophet (ﷺ) said, “He who wants to see a man from the dwellers of Jannah, let him look at him (bedouin).” [Al- Bukhari and Muslim, Riyad as-Salihin 1212]"
                ],
                [
                    "title" => "Narrated Ibn `Abbas",
                    "body" => "The Prophet (ﷺ) sent Mu`adh to Yemen and said, “Invite the people to testify that none has the right to be worshipped but Allah and I am Allah’s Messenger (ﷺ), and if they obey you to do so, then teach them that Allah has enjoined on them five prayers in every day and night (in twenty-four hours), and if they obey you to do so, then teach them that Allah has made it obligatory for them to pay the Zakat from their property and it is to be taken from the wealthy among them and given to the poor.” [Sahih al-Bukhari 1395]"
                ],
                [
                    "title" => "Narrated Abu Huraira",
                    "body" => "Allah’s Messenger (ﷺ) said, “Whoever is made wealthy by Allah and does not pay the Zakat of his wealth, then on the Day of Resurrection his wealth will be made like a baldheaded poisonous male snake with two black spots over the eyes. The snake will encircle his neck and bite his cheeks and say, ‘I am your wealth, I am your treasure.’ “Then the Prophet (ﷺ) recited the holy verses: — ‘Let not those who withhold . . .’ (to the end of the verse). (3.180). [Sahih al-Bukhari 1403]"
                ]
            ];

            $publications = [
                'zakatBidhibidhan' => 'Zakater Bidhibidhan',
                'guideToZakat' => 'A guide to Zakat',
                'zakatShahayika' => 'Zakat Shahayika',
                'zakatForm' => 'Zakat Calculation Form',
            ];

            $documents = null;

            foreach ($publications as $key => $title) {
                [$filePath, $fileExists] = $this->getPublicationFileDetails($title);
                if ($key == 'zakatBidhibidhan') {
                    $formattedKey = 'যাকাতের বিধিবিধান';
                } else if ($key == 'guideToZakat') {
                    $formattedKey = 'A guide to Zakat';
                } else if ($key == 'zakatShahayika') {
                    $formattedKey = 'যাকাত সহায়িকা';
                } else if ($key == 'zakatForm') {
                    $formattedKey = 'যাকাত ক্যালকুলেশন ফরম';
                }
                if ($fileExists) {
                    $documents[] = [
                        "title" => $formattedKey,
                        "pdf_url" => Storage::disk('public')->url($filePath)
                    ];
                }
            }

            $data = [
                'banner' => $banner->getImageUrl(),
                'total_sections' => 3,
                'content_sections' => $sections,
                'documents' => $documents
            ];

            return $this->success('Zakat In Hadith Page Content', $data);

        } catch (\Exception $e) {
            return $this->error(
                'Server error',
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }

    private function getDynamicImageUrl($relativeImagePath): string
    {
        if($relativeImagePath) return 'storage/admin/' . $relativeImagePath;

        return 'images/image_placeholder.png';
    }

    private function getPublicationFileDetails(string $title): array
    {
        $publication = Publication::where('publication_type', PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS)
            ->where('title', $title)
            ->first();

        if ($publication && $publication->attachment && $publication->attachment->file) {
            $filePath = '/admin/' . $publication->attachment->file;
            $fileExists = Storage::disk('public')->exists($filePath);
            return [$filePath, $fileExists];
        }

        return [null, false];
    }

    public function zakatFaq()
    {
        try {
            $faqs = [
                [
                    "question" => "What is the Center for Zakat Management (CZM)?",
                    "answer" => "See About CZM page: " . route('aboutUs')
                ],
                [
                    "question" => "What are the aims and objectives of the CZM?",
                    "answer" => "See About CZM page: " . route('aboutUs')
                ],
                [
                    "question" => "Who are behind the initiative of the CZM?",
                    "answer" => "Please visit the Governing body webpage."
                ],
                [
                    "question" => "Do the founders of the CZM have any business purpose?",
                    "answer" => "The founders of the CZM have been trying to comprehensively comply with the tenets of Islamic code in Zakat management. As the Zakatable assets are once paid, there remain no authority of the Zakat payers on the paid assets, money or in any form. So the founders of the institutions can’t accomplish any business pursuit from the initiative as well as the CZM can no way serve any business purpose of any zakat payer. So in a nutshell the response to the question is that the founders of the CZM have no business purpose in supporting, managing or collecting Zakat and its disbursement following the codes of Islam."
                ],
                [
                    "question" => "Where does the CZM spend zakat money and assets?",
                    "answer" => "As per the rulings of Zakat payment in the Quran and Hadith, the Zakat funds and assets could be utilized in basically eight sectors which eventually serve the humanity. The seven programs of the CZM and its projects follows only the eight set criteria of Zakat fund management."
                ],
                [
                    "question" => "Does my Zakat Payment to CZM comply with Shariah?",
                    "answer" => "As the CZM complies with the tenets of Islam in Zakat management and very much restrict to the eight area of spending zakat fund, so funds provided to the CZM complies with Shariah and its noble and higher objectives towards human welfare."
                ],
                [
                    "question" => "Does the CZM practice transparency in zakat money management?",
                    "answer" => "The CZM is humble to say it follows the system that is completely transparent. The entire management has been audited by renowned independent institutions with regular intervals. The CZM follows the directives and dedicated to accomplish the higher objectives of Zakat as per the codes of Islam. With a structured form, all the audit reports are public."
                ],
                [
                    "question" => "What will be the benefits if I pay Zakat to CZM instead of spending wherever I like?",
                    "answer" => "Individual Zakat payment may or may not achieve permanent goal of alleviating poverty and ensuring sustainable community development. Institutionalized Zakat management ensures the goal with a very structured form. As the basics of Islam encouraged plan of activities to achieve the satisfaction of Allah, the institutionalized form of Zakat management ensures gradual development of achieving the targets for serving the humanity. The previous records of Zakat management of the CZM reflects the reality.\n\nA single effort can make change. But if a lot of single efforts meet together to achieve higher objective and goal, the entire effort create the synergy. Which is a burden for a single hand a bunch of hands make the burden into an easy task. Combined efforts with concerted dedication towards achieving the basic objectives of Zakat always bring better result than expected.\n\nA single case story could be enough to define the case. From an individual’s Zakat fund, an orphan could get shelter or get some for the time being. But if the concerted effort works with very structured way it will bring better benefit. For an example in upgrading life standard of the orphans, in the structured form, Zakat funds could be collected and amassed, a pilot project could be undertaken to accomplish 50 orphans, let them access to vocational and technical training and later placement in different institutions. The process will ensure more sustainability rather than spending some money for daily meal of an orphan. So individual effort will be beneficial for the time being but concerted efforts of Zakat management will eventually ensure equitable sharing of wealth and promise a society free of poverty."
                ],
                [
                    "question" => "Is the CZM environment friendly?",
                    "answer" => "The prophet Muhammad (PBUH) has clearly said in his messages that every single place of the planet belongs to Allah and all the places a devotee can do Sijdah before the Almighty and it is duty of the believer to preserve the place of Sijdah. Whatever needs for sustainable preservation of the climate and environment, the CZM is entirely not only friendly to the environment rather the CZM feels it mandatory to the preservation of environment as per the directives of the message of Islam."
                ],
                [
                    "question" => "How can I calculate my Zakat?",
                    "answer" => "Please put your amounts of zakat payable assets in the calculator set in the website. It will take just a minute to calculate the exact figure."
                ],
                [
                    "question" => "How disability and gender equality is addressed through the activities of the CZM?",
                    "answer" => "The Center for Zakat Management is dedicated to promote the life standard of the distressed communities. As per the tenets of Zakat management with complete guidance of Islam the disabled people are of high priority to get the services from the Zakat funds. On the other hand, the CZM believes in the dignity of the women as directed in the Holy Quran that women are the covers of the men and men are the covers of women. As Islam has always uphold the excellence of dignity of the human kind, so there is no scope of gender discrimination in conducting zakat funds for accomplishing activities and projects of the CZM."
                ],
                [
                    "question" => "What if I do not pay Zakat to the CZM?",
                    "answer" => "It is in no way mandatory to pay Zakat to the CZM. The CZM collects the funds and try to use it in very structured way to institutionalize the management to achieve the basic goals and objectives of Zakat."
                ],
                [
                    "question" => "What if I do not pay Zakat at all?",
                    "answer" => "Please visit the Zakat in Quran and Hadit tab"
                ],
                [
                    "question" => "Do the corporate organizations need to pay zakat?",
                    "answer" => "Please visit the business zakat portion"
                ],
                [
                    "question" => "How business zakat could be determined?",
                    "answer" => "Please visit the business zakat tab"
                ],
                [
                    "question" => "Is there any tax waiver if a corporate organization pay zakat to the CZM?",
                    "answer" => "There is no tax waiver in Bangladesh if you do pay Zakat."
                ],
                [
                    "question" => "Are Zakat and Tax same?",
                    "answer" => "Not at all. Zakat is the third pillar of Islam and it is mandatory for every affluent Muslim who possess the certain amount of assets. But tax is levied by the government on its citizens."
                ],
                [
                    "question" => "Do the taxpayers need to pay Zakat?",
                    "answer" => "If the taxpayers have Zakat payable assets, s/he must have to pay Zakat."
                ],
                [
                    "question" => "If a Muslim country collect tax, do the citizens of the country need to pay Zakat?",
                    "answer" => "Zakat is a obligatory duty for the Muslims, Muslim majority country’s tax collection doesn’t full fill the obligation of Zakat."
                ],
                [
                    "question" => "Is there any type of waiver in Zakat from the total zakat payable assets?",
                    "answer" => "No, there is no waiver of Zakat from the Zakat payable assets/ money."
                ],
                [
                    "question" => "If there are other institutions for Zakat collection and disbursement, why I need to pay Zakat through the CZM?",
                    "answer" => "It depends on the trust on the organization, mission and vision of the organization, transparency of the zakat collector organization, expected outcomes of the organization, compliance with Shariah and others. So it totally depends on the Zakat payers."
                ],
                [
                    "question" => "Paying zakt to CZM and paying zakat to mosque, madrassha, orphanage, distressed women shelter- which one is more beneficial and pro-Shariah?",
                    "answer" => "It needs a long discussion to focus on the issue. There is no doubt that paying Zakat to such institutions are worthy and ways of achieving satisfaction of the Almighty. Indeed every single good work is beneficial and an way of serving the directives of the divine guidance of Islam.\n\nThe Center for Zakat Management never discourage to pay Zakat funds, assets and money to Mosque, Madrahsaah or of other institutions. The CZM always encourages any time of pro humanitarian works those correspond with the message and objectives of Zakat.\n\nAs every single task depend on the intention as per the quotes of the Prophet Muhammd (PBUH), so the determination of zakat payment to the CZM for management or payment to the mentioned institutions entirely depends on the zakat payer. In this concern the CZM finds its activities more structured and institutionalized and accomplish the activities in compliance with Shariah as like an individual zakat payer conduct the management in compliance with the guidance of Islam."
                ],
            ];

            $data = [
                'total_faq_' => count($faqs),
                'faqs' => $faqs
            ];

            return $this->success('Zakat FAQ Page Content', $data);

        } catch (\Exception $e) {
            return $this->error(
                'Server error',
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }
}

<x-main>
    @include('home.sections.banner')
    <div class="container">

        <p>
            <h4 class="text-dark"><strong>Abu Hurairah (May Allah be pleased with him) reported:</strong></h4>
            <br>
            A bedouin came to the Prophet (ﷺ) and said: “O Messenger of Allah! Direct me to a deed by which I may be entitled to enter Jannah.” The Prophet (ﷺ) said, “Worship Allah, and never associate anything with Him, establish Salat, pay the Zakat which has been enjoined upon you, and observe Saum of Ramadan.” He (the bedouin) said: “By Him in Whose Hand my soul is, I will never add anything to these (obligations).” When he turned his back, the Prophet (ﷺ) said, “He who wants to see a man from the dwellers of Jannah, let him look at him (bedouin).” [Al- Bukhari and Muslim,Riyad as-Salihin 1212]
        </p>

        <p>
            <h4 class="text-dark"><strong>Narrated Ibn `Abbas:</strong></h4>
            <br>
            The Prophet (ﷺ) sent Mu`adh to Yemen and said, “Invite the people to testify that none has the right to be worshipped but Allah and I am Allah’s Messenger (ﷺ), and if they obey you to do so, then teach them that Allah has enjoined on them five prayers in every day and night (in twenty-four hours), and if they obey you to do so, then teach them that Allah has made it obligatory for them to pay the Zakat from their property and it is to be taken from the wealthy among them and given to the poor.” [Sahih al-Bukhari 1395]
        </p>

        <p>
            <h4 class="text-dark"><strong>Narrated Abu Huraira:</strong></h4>
            <br>
            Allah’s Messenger (ﷺ) said, “Whoever is made wealthy by Allah and does not pay the Zakat of his wealth, then on the Day of Resurrection his wealth will be made like a baldheaded poisonous male snake with two black spots over the eyes. The snake will encircle his neck and bite his cheeks and say, ‘I am your wealth, I am your treasure.’ “Then the Prophet (ﷺ) recited the holy verses: — ‘Let not those who withhold . . .’ (to the end of the verse). (3.180). [Sahih al-Bukhari 1403]
        </p>

        @if($zakatBidhibidhanFileExists || $guideToZakatFileExists || $zakatShahayikaFileExists || $zakatFormFileExists)
            <p>
            <h4 class="text-dark"><strong>To know more about Zakat, please consult these materials:</strong></h4>
            <ol class="mt-4">
                @if ($zakatBidhibidhanFileExists)
                    <li>যাকাতের বিধিবিধান: <a href="{{ Storage::disk('public')->url($zakatBidhibidhanFilePath) }}" target="_blank"> {{ Storage::disk('public')->url($zakatBidhibidhanFilePath) }}</a></li>
                @endif
                @if ($guideToZakatFileExists)
                    <li>A guide to Zakat: <a href="{{ Storage::disk('public')->url($guideToZakatFilePath) }}" target="_blank"> {{ Storage::disk('public')->url($guideToZakatFilePath) }} </a></li>
                @endif
                @if ($zakatShahayikaFileExists)
                    <li>যাকাত সহায়িকা: <a href="{{ Storage::disk('public')->url($zakatShahayikaFilePath) }}" target="_blank"> {{ Storage::disk('public')->url($zakatShahayikaFilePath) }} </a></li>
                @endif
                @if ($zakatFormFileExists)
                    <li>যাকাত ক্যালকুলেশন ফরম: <a href="{{ Storage::disk('public')->url($zakatFormFilePath) }}" target="_blank"> {{ Storage::disk('public')->url($zakatFormFilePath) }} </a></li>
                @endif
            </ol>
            </p>
        @endif
    </div>
</x-main>

<x-main>
    @include('home.sections.banner')
    <div class="container">
        <h3 class="text-dark"><strong>Personal Zakat</strong></h3>

        <p>Islam encourages trade, entrepreneurial activity and the equitable distribution of wealth. This attitude is often manifested through the act of giving in charity. Whilst certain types of charity (Sadaqah) are optional, Islam has made the payment of Zakat an obligation upon the Muslims whose wealth reaches a certain threshold.</p>

        <h3 class="text-dark"><strong>The Importance of Zakat</strong></h3>
        <p>The Quran commands Muslims to: “Establish regular prayer and give Zakat.” (73:20)</p>

        <p>Allah’s Messenger (ﷺ) said, “Whoever is made wealthy by Allah and does not pay the Zakat of his wealth, then on the Day of Resurrection his wealth will be made like a baldheaded poisonous male snake with two black spots over the eyes. The snake will encircle his neck and bite his cheeks and say, ‘I am your wealth, I am your treasure.’ ” Then the Prophet (ﷺ) recited the holy verses:– ‘Let not those who withhold . . .’ (to the end of the verse). (3.180).</p>
        <p>[Sahih al-Bukhari 1403]</p>

        <p>Scholars infer the giving of Zakat has both an outer and an inner dimension. The outer dimension involves the purification of a Muslim’s wealth. Zakat serves as a filter to keep out impurities from a believer’s wealth and for that wealth to then increase in a manner pleasing to Allah. The inner dimension suppresses the ego, and quells ugly tendencies such as greed, jealousy and miserliness.</p>

        <h3 class="text-dark"><strong>What is ZAKAT?</strong></h3>
        <p>Zakat is commonly referred to as either a tax or as charity. Neither of these is accurate as tax is a legal obligation whilst charity is voluntary. Zakat however, is a divine duty. Zakat is considered a right of the poor over the rich. Withholding it is tantamount to depriving the poor of their God-given right.</p>

        <p>Allah (SWT) says of the wealthy in the Qur’an, “In their wealth there is a known share for the beggars and the destitute.” (70:24-25)</p>
        <p>“Take alms from their property that you may purify and sanctify them and pray for them. Verily your prayers are a comfort for them.” (9:103)</p>

        <h3 class="text-dark"><strong>Definition of Nisab</strong></h3>
        <p>The Nisab is the minimum amount of wealth upon which Zakat is payable. It represents the guaranteed minimum financial floor above which an individual is deemed to be ‘Sahib-un-Nisab’ and therefore liable to Zakat. The Nisab limit was set by the Prophet (SAW) at 20 Mithqual of Gold or 200 Dirham of Silver. This measure is equivalent to 85 grams of Gold or 595 grams of Silver. If a person only has Gold as an asset, then the Nisab measure for Gold must be used. If, however the person has a mixture of assets, then the Nisab level for Silver (595 grams) should be used. To ascertain the current monetary equivalent of the Nisab limit, it is necessary to establish the market rate for a gram of Gold or Silver and calculate accordingly.</p>

        <p class="text-dark"><strong>Who Pays Zakat?</strong></p>
        <ul>
            <li>Sane*</li>
            <li>Adult (have reached puberty) *</li>
            <li>Owner of Wealth</li>
            <li>Muslim (Zakat is not paid by non-Muslims)</li>
            <li>Sahib-un-Nisab (Owner of wealth above the level of Nisab)</li>
        </ul>

        <p>*According to Maliki, Hanbali and Shafi Madhab, there is Zakat on the wealth of Minor and Insane person if total wealth reaches nisab threshold. On behalf of them, their guardian needs to pay Zakat.</p>

        <p class="text-dark"><strong>Assets subjects to Zakat:</strong></p>
        <p class="text-dark"><strong>Personal Assets</strong></p>
        <p>
            All assets owned for personal use and used to meet basic living expenses are completely exempt from Zakat EXCEPT for Cash and items made from Gold & Silver.
        </p>

        <p class="text-dark"><strong>Trading Assets
            </strong>
        </p>

        <p>
            All assets purchased for the explicit purpose of resale are liable for Zakat. Assets purchased with the express intention of generating a rental / investment return are not themselves liable to Zakat.
        </p>


        <p>Zakat is always paid on the balance of assets owned on the Zakatable date, not on fluctuating amounts during the year or even an average amount.</p>

        <p class="text-dark"><strong>Please note Zakat is not payable on HARAAM income. The entire value of Haraam income must be given in charity.</strong></p>


        <br>
        <p class="text-dark"><strong>There are principally five categories of Assets which are subject to Zakat:</strong></p>
        <ol>
            <li>Gold & Silver</li>
            <li>Cash & Liquid Investments</li>
            <li>Business Assets (includes Stocks, Shares, Property and Pensions)</li>
            <li>Livestock</li>
            <li>Agricultural Output</li>
        </ol>

        <h3 class="text-dark mt-4"><strong>Who Should Receive Zakat?</strong></h3>
        <p class="text-dark mt-3"><strong>Those entitled to receive Zakat:</strong></p>
        <p>The Quran, in Surah 9 verse 60 mentions those categories of people entitled to receive Zakat. The primary recipients of Zakat are the poor and destitute. These can be defined as those people whose total assets (not just the five categories of Zakatable assets as shown on page 7) are less than the Nisab level.</p>

        <p class="text-dark"><strong>The following persons are specifically excluded from receiving Zakat:</strong></p>
        <ul>
            <li>Al Hashimi (Descendant of the Prophet SAW)</li>
            <li>The payer’s son or grandson</li>
            <li>The payer’s father or grandfather</li>
            <li>The payer’s spouse</li>
        </ul>

        <p>Preference should be given to relatives over the general public as Islam places great emphasis on the virtue of family ties. When giving Zakat, it is not necessary to inform the recipient of the nature of payment. (E.g. you can disguise the Zakat Payment as a gift).</p>

        @if($zakatBidhibidhanFileExists || $guideToZakatFileExists || $zakatShahayikaFileExists || $zakatFormFileExists)
            <p>
            <h4 class="text-dark"><strong>To know more about Zakat, please consult these materials:</strong></h4>
            <ol>
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

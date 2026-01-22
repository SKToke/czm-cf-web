@php
use App\Models\Program;
@endphp

<nav class="mainmenu-area stricky">
    <div class="d-flex justify-content-center">
        <div class="navigation pull-left">
            <div class="nav-header">
                <ul>
                    <li>
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="dropdown">
                        <a href="#" onclick="return false;" style="cursor: default;">
                            About
                            <i class="fa-solid fa-caret-down ms-2"></i>
                        </a>
                        <ul class="submenu shadow">
                            <li>
                                <a href="{{ route('aboutUs') }}">About Us</a>
                            </li>
                            <li>
                                <a href="{{ route('czm-governance') }}">CZM Governance</a>
                            </li>
                            <li>
                                <a href="{{ route('accountability') }}">CZM Accountability</a>
                            </li>
                            <li>
                                <a href="{{ route('photo-gallery') }}">Photo Gallery</a>
                            </li>
                            <li>
                                <a href="{{ route('video-gallery') }}">Video Gallery</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" onclick="return false;" style="cursor: default;">
                            Learn Zakat
                            <i class="fa-solid fa-caret-down ms-2"></i>
                        </a>
                        <ul class="submenu shadow">
                            <li>
                                <a href="{{ route('quranic_verses') }}">Zakat in Quran</a>
                            </li>
                            <li>
                                <a href="{{ route('zakatInHadiths') }}">Zakat in Hadith</a>
                            </li>
                            <li>
                                <a href="{{ route('personalZakat') }}">Personal Zakat</a>
                            </li>
                            <li>
                                <a href="{{ route('businessZakat') }}">Business Zakat</a>
                            </li>
                            <li>
                                <a href="{{ route('zakatOnAgriculture') }}">Ushar/ Zakat on Agriculture</a>
                            </li>
                            <li>
                                <a href="{{ route('sadaqah') }}">Sadaqah</a>
                            </li>
                            <li>
                                <a href="{{ route('cashWaqf') }}">Waqf/ Cash Waqf</a>
                            </li>
                            <li>
                                <a href="{{ route('qard_al_hasan') }}">Qard al-Hasan</a>
                            </li>
                            <li>
                                <a href="{{ route('video-lessons') }}">Video & Lectures</a>
                            </li>
                            <li>
                                <a href="{{ route('blogs') }}">Blogs & Articles</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" onclick="return false;" style="cursor: default;">
                            Programs
                            <i class="fa-solid fa-caret-down ms-2"></i>
                        </a>
                        <ul class="submenu shadow">
                            <li>
                                <a href="{{ route('programs') }}">All Programs</a>
                            </li>
                            @foreach(Program::getDefaults() as $program)
                                <li>
                                    <a href="{{ route('program-details', ['slug' => $program->slug]) }}">
                                        {{ $program->title_with_subtitle() }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('campaigns') }}">Support Campaigns</a>
                    </li>
                    <li class="dropdown">
                        <a href="#" onclick="return false;" style="cursor: default;">
                            Stories
                            <i class="fa-solid fa-caret-down ms-2"></i>
                        </a>
                        <ul class="submenu shadow">
                            <li>
                                <a href="{{ route('news') }}">News</a>
                            </li>
                            <li>
                                <a href="{{ route('success_stories') }}">Success Stories</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="{{ route('publications') }}">
                            Publication
                            <i class="fa-solid fa-caret-down ms-2"></i>
                        </a>
                        <ul class="submenu shadow">
                            <li>
                                <a href="{{ route('books') }}">Book</a>
                            </li>
                            <li>
                                <a href="{{ route('auditReports') }}">Audit Report</a>
                            </li>
                            <li>
                                <a href="{{ route('newsletters') }}">Newsletter</a>
                            </li>
                            <li>
                                <a href="{{ route('reports') }}">Report</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('notices') }}">Announcement</a>
                    </li>
                    <li>
                        <a href="{{ route('contact-us.index') }}">Contact Us</a>
                    </li>
                </ul>
            </div>
            <div class="nav-footer">
                <button>
                    <i class="fa fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

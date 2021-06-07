<div class="page-footer">
    <div class="page-footer-inner">
        <ul>
            <li><strong>Last Login: </strong>{{ Carbon\Carbon::parse(Auth::user()->last_login)->format('l, d F Y') }}</li>
            <li><strong>Help Desk: </strong>+254 20 5252 453 / +254 711 408 108 / +254 736 584 876</li>
            <li><strong>Chat With Us</strong></li>
            <li><a href="mailto:info@wizag.biz"><strong>Report Issue</strong></a></li>
        </ul>
        <ul>
            <li><a href="https://wizag.biz" target="_blank">Wise & Agile Solutions Limited</a> &copy;{{ \Carbon\Carbon::now()->year }} - All Rights Limited</li>
            <li>Executive Sales Partner - <a href="http://tikone.biz" target="_blank">Tikone Solutions Limited</a></li>
        </ul>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<div class="page-footer">
    <div class="page-footer-inner">
        <ul>
            <li><strong>Last Login: </strong>{{ Carbon\Carbon::parse(Auth::user()->last_login)->format('l, d F Y') }}</li>
            <li><strong>Help Desk: </strong>+254 20 XXXX XXX / +254 XXX XXX XXX / +254 XXX XXX XXX</li>
            <li><strong>Chat With Us</strong></li>
            <li><a href="mailto:demo@example.com"><strong>Report Issue</strong></a></li>
        </ul>
        <ul>
            <li><a href="https://example.com" target="_blank">{{ ucwords(config('app.name')) }}</a> &copy;{{ \Carbon\Carbon::now()->year }} - All Rights Limited</li>
            <li>Executive Sales Partner - <a href="{{ url('/') }}" target="_blank">{{ ucwords(config('app.name')) }}</a></li>
        </ul>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
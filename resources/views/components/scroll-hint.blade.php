{{-- Rotating "scroll down" ring with the arrow button in the middle. --}}
<div class="mil-circle-text-position">
    <div class="mil-circle-text mil-rotate mil-accent-inside" data-value="360">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" enable-background="new 0 0 300 300" xml:space="preserve" aria-hidden="true" focusable="false">
            <defs>
                <path id="circlePath" d="M 150, 150 m -60, 0 a 60,60 0 0,1 120,0 a 60,60 0 0,1 -120,0 " />
            </defs>
            <circle cx="150" cy="100" r="75" fill="none" />
            <g>
                <use xlink:href="#circlePath" fill="none" />
                <text class="mil-link mil-dark" style="letter-spacing: .75rem">
                    <textPath xlink:href="#circlePath">Scroll down - Scroll down - </textPath>
                </text>
            </g>
        </svg>
    </div>
    <a href="#scroll" class="far fa-arrow-down mil-bg-m-1 mil-a-1" aria-label="Scroll down"></a>
</div>

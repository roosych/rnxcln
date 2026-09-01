document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    // Register GSAP plugins
    gsap.registerPlugin(ScrollTrigger);

    /* -------------------------------------------
    
    page transitions
    
    ------------------------------------------- */
    const options = {
        containers: ['#swupMain', '#swupMenu'],
        animateHistoryBrowsing: true,
        linkSelector: 'a:not([data-no-swup]):not([href^="#"])',
        plugins: [new SwupBodyClassPlugin()]
    };

    const swup = new Swup(options);

    /* -------------------------------------------
    
    smooth scroll
    
    ------------------------------------------- */
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        orientation: 'vertical',
        smoothWheel: true,
        wheelMultiplier: 1,
        smoothTouch: false,
        touchMultiplier: 2,
        infinite: false
    });

    lenis.on('scroll', ScrollTrigger.update);

    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });

    gsap.ticker.lagSmoothing(0);

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }

    /* -------------------------------------------
    
    menu
    
    ------------------------------------------- */
    const menuBtn = document.querySelector('.mil-menu-btn');
    const mainMenu = document.querySelector('.mil-main-menu');
    const topPanel = document.querySelector('.mil-top-panel');
    const menuLinks = document.querySelectorAll('.mil-main-menu a');

    if (menuBtn && mainMenu) {
        menuBtn.addEventListener('click', function () {
            menuBtn.classList.toggle('mil-active');
            mainMenu.classList.toggle('mil-active');
        });
    }

    menuLinks.forEach(link => {
        link.addEventListener('click', function () {
            if (menuBtn && mainMenu) {
                menuBtn.classList.remove('mil-active');
                mainMenu.classList.remove('mil-active');
            }
        });
    });

    window.addEventListener('scroll', function () {
        if (mainMenu && topPanel) {
            if (window.scrollY > 10) {
                mainMenu.classList.add('mil-scroll');
                topPanel.classList.add('mil-scroll');
            } else {
                mainMenu.classList.remove('mil-scroll');
                topPanel.classList.remove('mil-scroll');
            }
        }
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const offsetPosition = targetElement.getBoundingClientRect().top + window.scrollY - parseFloat(getComputedStyle(document.documentElement).fontSize) * 20;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    /* -------------------------------------------
    
    right buttons
    
    ------------------------------------------- */
    const milRightButtonsFrame = document.querySelector('.mil-right-buttons-frame');
    const milOpenWindow = document.querySelector('.mil-open-window');
    const milOrderCallWindow = document.querySelector('.mil-order-call-window');
    const milBackToTop = document.querySelector('.mil-back-to-top');

    if (milRightButtonsFrame && milBackToTop) {
        window.addEventListener('scroll', function () {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop + windowHeight >= documentHeight - 100) {
                milRightButtonsFrame.classList.add('mil-on-bottom');
            } else {
                milRightButtonsFrame.classList.remove('mil-on-bottom');
            }

            if (scrollTop >= 200) {
                milBackToTop.classList.add('mil-active');
            } else {
                milBackToTop.classList.remove('mil-active');
            }
        });
    }

    if (milOpenWindow && milOrderCallWindow) {
        milOpenWindow.addEventListener('click', function () {
            this.classList.toggle('mil-active');
            milOrderCallWindow.classList.toggle('mil-active');
        });
    }

    /* -------------------------------------------

    sliders
    
    ------------------------------------------- */
    const initSliders = () => {
        const reviewsSliderEl = document.querySelector('.mil-reviews-slider');
        const teamSliderEl = document.querySelector('.mil-team-slider');

        if (reviewsSliderEl) {
            new Swiper(reviewsSliderEl, {
                pagination: {
                    el: '.mil-revi-pagination',
                    clickable: true,
                },
                speed: 800,
                effect: 'fade',
                parallax: true,
                navigation: {
                    nextEl: '.mil-revi-next',
                    prevEl: '.mil-revi-prev',
                },
            });
        }

        if (teamSliderEl) {
            new Swiper(teamSliderEl, {
                spaceBetween: 15,
                slidesPerView: 2,
                loop: true,
                speed: 5000,
                autoplay: {
                    delay: 0,
                },
                freeMode: true,
                breakpoints: {
                    768: {
                        slidesPerView: 4,
                    },
                    992: {
                        slidesPerView: 7,
                    },
                },
            });
        }
    };

    initSliders();

    /* -------------------------------------------
    
    scroll animation

    ------------------------------------------- */
    const initScrollAnimations = () => {
        const animateSections = (selector, animationProps, scrollTriggerConfig) => {
            document.querySelectorAll(selector).forEach((section) => {
                const props = animationProps(section);
                const config = scrollTriggerConfig(section);
                gsap.fromTo(section, props.from, {
                    ...props.to,
                    scrollTrigger: config
                });
            });
        };

        // Fade In Animation
        animateSections(".mil-up",
            () => ({
                from: {
                    opacity: 0,
                    y: 60,
                    ease: 'sine'
                },
                to: {
                    opacity: 1,
                    y: 0
                }
            }),
            (section) => ({
                trigger: section,
                toggleActions: 'play none none reverse'
            })
        );

        // Parallax Animation
        animateSections(".mil-parallax-img",
            (section) => ({
                from: {
                    y: section.getAttribute("data-value-1"),
                    ease: 'sine'
                },
                to: {
                    y: section.getAttribute("data-value-2")
                }
            }),
            (section) => ({
                trigger: section,
                scrub: true,
                toggleActions: 'play none none reverse'
            })
        );

        // Rotate Animation
        animateSections(".mil-rotate",
            (section) => ({
                from: {
                    rotate: 0,
                    ease: 'sine'
                },
                to: {
                    rotate: section.getAttribute("data-value")
                }
            }),
            (section) => ({
                trigger: section,
                scrub: true,
                toggleActions: 'play none none reverse'
            })
        );

        // Scale Animation
        animateSections(".mil-scale-img",
            (section) => ({
                from: {
                    scale: section.getAttribute("data-value-1"),
                    ease: 'sine'
                },
                to: {
                    scale: section.getAttribute("data-value-2")
                }
            }),
            (section) => ({
                trigger: section,
                scrub: true,
                toggleActions: 'play none none reverse'
            })
        );

        // Scale Animation with Y offset
        animateSections(".mil-scale-img-2",
            (section) => ({
                from: {
                    y: '-130',
                    scale: section.getAttribute("data-value-1"),
                    ease: 'sine'
                },
                to: {
                    y: '0',
                    scale: section.getAttribute("data-value-2")
                }
            }),
            (section) => ({
                trigger: section,
                end: "top top+=120",
                scrub: true,
                toggleActions: 'play none none reverse'
            })
        );

        // Scale Animation starting from top offset
        animateSections(".mil-scale-img-top",
            (section) => ({
                from: {
                    scale: section.getAttribute("data-value-1"),
                    ease: 'sine'
                },
                to: {
                    scale: section.getAttribute("data-value-2")
                }
            }),
            (section) => ({
                trigger: section,
                scrub: true,
                start: "top top+=120",
                toggleActions: 'play none none reverse'
            })
        );

        // Counter Animation
        document.querySelectorAll(".mil-counter").forEach(element => {
            const zero = {
                val: 0
            };
            const num = parseFloat(element.dataset.number);
            const decimals = num.toString().split(".")[1]?.length || 0;

            gsap.to(zero, {
                val: num,
                duration: 1.8,
                scrollTrigger: {
                    trigger: element,
                    toggleActions: 'play none none reverse'
                },
                onUpdate: () => {
                    element.textContent = zero.val.toFixed(decimals);
                }
            });
        });
    };


    initScrollAnimations();

    /* -------------------------------------------
    
    accordion
    
    ------------------------------------------- */
    const initAccordion = () => {
        const accordions = document.querySelectorAll(".mil-accordion");

        accordions.forEach(button => {
            button.addEventListener("click", () => {
                const panel = button.nextElementSibling;
                const icon = button.querySelector(".mil-icon");

                accordions.forEach(otherButton => {
                    if (otherButton !== button) {
                        otherButton.classList.remove("mil-active");
                        if (otherButton.querySelector(".mil-icon")) {
                            otherButton.querySelector(".mil-icon").textContent = "+";
                        }
                        if (otherButton.nextElementSibling) {
                            otherButton.nextElementSibling.style.maxHeight = null;
                        }
                    }
                });

                button.classList.toggle("mil-active");
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                    icon.textContent = "+";
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                    icon.textContent = "−";
                }

                ScrollTrigger.refresh();
            });
        });
    };

    initAccordion();



    /* -------------------------------------------
        
    forms

    ------------------------------------------- */

    const initForms = () => {
        var phoneInputs = document.querySelectorAll('.mil-phone-input');

        phoneInputs.forEach(function (phoneInput) {
            var cleave = new Cleave(phoneInput, {
                delimiters: ['(', ')', '-'],
                blocks: [2, 3, 3, 4],
                prefix: '+1',
                numericOnly: true,
                noImmediatePrefix: true,
            });

            phoneInput.addEventListener('focus', function () {
                if (phoneInput.value === '') {
                    phoneInput.value = '+1';
                }
            });

            phoneInput.addEventListener('blur', function () {
                if (phoneInput.value === '+1' || phoneInput.value === '+1(') {
                    phoneInput.value = '';
                }
            });
        });

        if (typeof flatpickr !== 'undefined') {
            document.querySelectorAll('.mil-date-input').forEach(function (dateInput) {
                if (dateInput._flatpickr) {
                    dateInput._flatpickr.destroy();
                }

                flatpickr(dateInput, {
                    dateFormat: 'F j, Y',
                    minDate: 'today',
                    monthSelectorType: 'static',
                    disableMobile: true,
                });
            });
        }
    };
    initForms();

    /* -------------------------------------------

    ajax forms

    ------------------------------------------- */

    // Opt-in via [data-ajax] on the <form> — submits with fetch instead of a
    // normal page POST, shows a status line and per-field errors in place.
    // The form still has a real action/method, so it degrades to a normal
    // submit if JS fails to load.
    const initAjaxForms = () => {
        document.querySelectorAll('form[data-ajax]').forEach((form) => {
            if (form.dataset.ajaxBound) return;
            form.dataset.ajaxBound = '1';

            const statusEl = form.querySelector('[data-form-status]');
            const submitBtn = form.querySelector('[type="submit"]');
            const submitLabel = submitBtn ? submitBtn.textContent : '';

            const setStatus = (text, isError = false) => {
                if (!statusEl) return;
                statusEl.textContent = text;
                statusEl.classList.toggle('mil-hidden', !text);
                statusEl.classList.toggle('mil-error-1', isError);
                statusEl.classList.toggle('mil-success-1', !isError);
            };

            const clearFieldErrors = () => {
                form.querySelectorAll('[data-error-for]').forEach((el) => { el.textContent = ''; });
            };

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                clearFieldErrors();
                setStatus('');

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Sending...';
                }

                fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json' },
                    body: new FormData(form),
                })
                    .then((response) => response.json().then((data) => ({ ok: response.ok, status: response.status, data })))
                    .then(({ ok, status, data }) => {
                        if (ok) {
                            setStatus(data.message || 'Thanks — we got your request.');
                            form.reset();
                            form.querySelectorAll('.mil-custom-select .mil-selected-value').forEach((el) => {
                                el.textContent = el.dataset.defaultLabel || el.textContent;
                                el.classList.remove('mil-selected');
                                el.closest('.mil-custom-select')?.classList.remove('mil-active');
                            });
                            return;
                        }

                        if (status === 422 && data.errors) {
                            Object.keys(data.errors).forEach((field) => {
                                const errorEl = form.querySelector('[data-error-for="' + field + '"]');
                                if (errorEl) errorEl.textContent = data.errors[field][0];
                            });
                            setStatus(data.message || 'Please check the form and try again.', true);
                            return;
                        }

                        setStatus(status === 429
                            ? "You're sending requests too fast — please wait a minute and try again."
                            : 'Something went wrong. Please call us instead, or try again in a moment.', true);
                    })
                    .catch(() => {
                        setStatus('Something went wrong. Please check your connection and try again.', true);
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = submitLabel;
                        }
                    });
            });
        });
    };
    initAjaxForms();

    /* -------------------------------------------
            
    custom select
            
    ------------------------------------------- */
    const initSelect = () => {
        document.querySelectorAll(".mil-custom-select").forEach((customSelect) => {
            const selectBtn = customSelect.querySelector(".mil-select-button");
            const selectedValue = customSelect.querySelector(".mil-selected-value");
            const optionsList = customSelect.querySelectorAll(".mil-select-dropdown li");

            if (selectBtn && selectedValue && optionsList.length > 0) {
                selectBtn.addEventListener("click", () => {
                    customSelect.classList.toggle("mil-active");
                });

                optionsList.forEach((option) => {
                    function handler(e) {
                        if (e.type === "click" && e.clientX !== 0 && e.clientY !== 0) {
                            selectedValue.textContent = option.children[1].textContent;
                            customSelect.classList.remove("mil-active");
                            if (!selectedValue.classList.contains("mil-selected")) {
                                selectedValue.classList.add("mil-selected");
                            }
                        }
                        if (e.key === "Enter") {
                            selectedValue.textContent = option.textContent;
                            customSelect.classList.remove("mil-active");
                            if (!selectedValue.classList.contains("mil-selected")) {
                                selectedValue.classList.add("mil-selected");
                            }
                        }
                    }
                    option.addEventListener("keyup", handler);
                    option.addEventListener("click", handler);
                });
            }
        });
    };
    initSelect();

    // Closes any open custom select when the click lands outside it. Kept
    // outside initSelect() and registered once (not re-run on
    // swup:contentReplaced like initSelect() is below) — it re-queries
    // ".mil-active" live on every click, so it keeps working for selects
    // swup swaps in later without piling up duplicate listeners.
    document.addEventListener("click", (e) => {
        document.querySelectorAll(".mil-custom-select.mil-active").forEach((customSelect) => {
            if (!customSelect.contains(e.target)) {
                customSelect.classList.remove("mil-active");
            }
        });
    });

    /* -------------------------------------------
                    
    before/after
                    
    ------------------------------------------- */
    const initBF = () => {
        var subject = document.querySelector('.mil-before-and-after');
        var scraper = document.querySelector('.mil-subject-scraper');
        var after = document.querySelector('.mil-subject-after');

        if (!subject || !scraper || !after) return;

        var distance = (window.innerWidth - subject.clientWidth) / 2;
        window.onresize = recalculateDistance;
        var px = 0;
        var touches = [];

        subject.addEventListener('mousemove', dragScraper, false);
        subject.addEventListener('touchmove', dragScraper, false);

        function recalculateDistance() {
            distance = (window.innerWidth - subject.clientWidth) / 2;
        }

        function dragScraper(event) {
            px = event.clientX - distance;

            if (px == null) {
                touches = event.touches;
                px = touches[0].clientX - distance;
            }
            if (px < 0) {
                px = 0;
            }
            scraper.style.transform = 'translate(' + px + 'px, 0)';
            after.style.transform = 'translate(-' + px + 'px, 0)';
        }
    };

    initBF();



    /* -------------------------------------------
                            
    fancybox
                            
    ------------------------------------------- */
    const initFancybox = () => {
        const galleryItems = document.querySelectorAll('[data-fancybox="gallery"]');

        Fancybox.defaults.Hash = false;

        Fancybox.bind('[data-fancybox="gallery"]', {
            loop: true,
            toolbar: true,
            buttons: ["zoom", "close"],
        });
    };

    initFancybox();

    /*----------------------------------------------------------
    ------------------------------------------------------------

    REINIT

    ------------------------------------------------------------
    ----------------------------------------------------------*/
    document.addEventListener("swup:contentReplaced", function () {
        window.scrollTo(0, 0);
        lenis.scrollTo(0, {
            immediate: true
        });
        /* -------------------------------------------

        menu

        ------------------------------------------- */
        const menuBtn = document.querySelector('.mil-menu-btn');
        const mainMenu = document.querySelector('.mil-main-menu');
        const menuLinks = document.querySelectorAll('.mil-main-menu a');

        function toggleMenu() {
            menuBtn.classList.toggle('mil-active');
            mainMenu.classList.toggle('mil-active');
        }

        menuLinks.forEach(link => {
            link.addEventListener('click', toggleMenu);
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const offsetPosition = targetElement.getBoundingClientRect().top + window.scrollY - parseFloat(getComputedStyle(document.documentElement).fontSize) * 20;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
        initAccordion();
        initSliders();
        initScrollAnimations();
        initForms();
        initAjaxForms();
        initSelect();
        initBF();
        initFancybox();
        ScrollTrigger.refresh();
    });
});

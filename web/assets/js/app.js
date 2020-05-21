var cs = {
    map: null,
    mapAutoMove: false,
    handlers: {
        'selecteer-ondernemer': function() {
            var onderneming = document.getElementById('onderneming');
            var locatie = document.getElementById('locatie');
            var street = this.querySelector('.straat').textContent;
            var house = this.querySelector('.huisnummer').textContent;
            onderneming.querySelector('span').innerHTML = this.querySelector('h3').textContent;
            onderneming.previousElementSibling.value = this.getAttribute('data-id');
            locatie.querySelector('span').innerHTML = street;
            document.getElementById('notitie_form_straat').value = street;
            document.getElementById('notitie_form_huisnummer').value = house;
            if (this.hasAttribute('data-geo')) {
                var point = this.getAttribute('data-geo').substring(16);
                point = point.substring(0, point.length -1).split(' ');
                if (cs.map) {
                    cs.mapAutoMove = true;
                    cs.map.panTo(point);
                }
            }
        },
        'click': function(ev) {
            location.hash = this.getAttribute('data-href');
        },
        'toggle-state': function(ev) {
            if (this.checked) {
                this.parentNode.classList.add('active');
            } else {
                this.parentNode.classList.remove('active');
            }
        },
        'toggle-visibility': function() {
            var i, hide = this.getAttribute('data-hide') && this.getAttribute('data-hide').split(' '), show = this.getAttribute('data-show') && this.getAttribute('data-show').split(' ');
            if (hide) {
                for (i = 0; i < hide.length; i++) {
                    document.getElementById(hide[i]).classList.add('is-hidden');
                }
            }
            if (show) {
                for (i = 0; i < show.length; i++) {
                    document.getElementById(show[i]).classList.remove('is-hidden');
                }
            }
        },
        'go-back': function(ev) {
            history.back();
            ev.preventDefault();
        },
        'start-location-picker': function() {
            setTimeout(function() {
                var container = document.getElementById('location-picker');
                var geo = document.getElementById('location-picker-geo');
                var center = [52.373290, 4.893465];
                var point;
                var streetInput = container.getAttribute('data-street') && document.getElementById(container.getAttribute('data-street'));
                if (geo.value != '') {
                    point = geo.value.substring(16);
                    center = point.substring(0, point.length -1).split(' ');
                }
                if (cs.locationPicker) {
                    cs.locationPicker.invalidateSize();
                } else {
                    cs.locationPicker = L.map(container, {
                        center: center,
                        zoom: 16,
                        attributionControl: false,
                        layers: [
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                minZoom: 14,
                                maxZoom: 19
                            })
                        ]
                    });
                    cs.locationPicker.on('moveend', function() {
                        var center = cs.locationPicker.getCenter();
                        geo.value = 'POINT(' + center.lat + ' ' + center.lng + ')';
                        if (streetInput && streetInput.value == '') {
                            cs.utils.fetchStreet(center.lat, center.lng, function(street) {
                                streetInput.value = street;
                            });
                        }
                    });
                }
            }, 300);
        },
        'toggle-service': function(ev) {
            var text = 'Zeker weten?';
            if (this.getAttribute('data-confirm')) {
                text = this.getAttribute('data-confirm');
            }
            if (confirm(text)) {
                var el = this.parentNode.parentNode;
                var form = this.form;
                var ticketsUrl = this.getAttribute('data-tickets-url');
                setTimeout(function() {
                    cs.utils.xhr({
                        url: form.action,
                        method: form.method,
                        timeout: 2000,
                        success: function(request) {
                            cs.utils.xhr({
                                url: ticketsUrl,
                                method: 'GET',
                                success: function(req) {
                                    var dummy = document.createElement('div');
                                    dummy.innerHTML = req.responseText;
                                    document.querySelector('.header').innerHTML = dummy.querySelector('.header').innerHTML;
                                }
                            });
                        },
                        error: function(request) {
                            el.classList.toggle('active');
                        }
                    });
                    el.classList.toggle('active');
                }, 200);
            }
            ev.preventDefault();
        },
        'edit-location': function() {
            document.getElementById('map-container').classList.remove('is-inactive');
        },
        'use-my-location': function() {
            var kompas = document.getElementById('kompas');
            kompas.innerHTML = '<title>Bezig met laden</title><path d="M12 4c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4zM20.485 7.515c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4zM26 16c0-1.105 0.895-2 2-2s2 0.895 2 2c0 1.105-0.895 2-2 2s-2-0.895-2-2zM22.485 24.485c0-1.105 0.895-2 2-2s2 0.895 2 2c0 1.105-0.895 2-2 2s-2-0.895-2-2zM14 28c0 0 0 0 0 0 0-1.105 0.895-2 2-2s2 0.895 2 2c0 0 0 0 0 0 0 1.105-0.895 2-2 2s-2-0.895-2-2zM5.515 24.485c0 0 0 0 0 0 0-1.105 0.895-2 2-2s2 0.895 2 2c0 0 0 0 0 0 0 1.105-0.895 2-2 2s-2-0.895-2-2zM4.515 7.515c0 0 0 0 0 0 0-1.657 1.343-3 3-3s3 1.343 3 3c0 0 0 0 0 0 0 1.657-1.343 3-3 3s-3-1.343-3-3zM1.75 16c0-1.243 1.007-2.25 2.25-2.25s2.25 1.007 2.25 2.25c0 1.243-1.007 2.25-2.25 2.25s-2.25-1.007-2.25-2.25z"></path>';
            kompas.classList.add('is-spinning');
            navigator.geolocation.getCurrentPosition(function(position) {
                kompas.classList.remove('is-spinning');
                kompas.innerHTML = '<title>Mijn locatie</title><path d="M17 32c-0.072 0-0.144-0.008-0.217-0.024-0.458-0.102-0.783-0.507-0.783-0.976v-15h-15c-0.469 0-0.875-0.326-0.976-0.783s0.129-0.925 0.553-1.123l30-14c0.381-0.178 0.833-0.098 1.13 0.199s0.377 0.749 0.199 1.13l-14 30c-0.167 0.358-0.524 0.577-0.906 0.577zM5.508 14h11.492c0.552 0 1 0.448 1 1v11.492l10.931-23.423-23.423 10.931z"></path>';
                if (cs.map) {
                    cs.map.panTo([position.coords.latitude, position.coords.longitude]);
                }
            }, function(error) {
                kompas.classList.remove('is-spinning');
                kompas.innerHTML = '<title>Mijn locatie</title><path d="M17 32c-0.072 0-0.144-0.008-0.217-0.024-0.458-0.102-0.783-0.507-0.783-0.976v-15h-15c-0.469 0-0.875-0.326-0.976-0.783s0.129-0.925 0.553-1.123l30-14c0.381-0.178 0.833-0.098 1.13 0.199s0.377 0.749 0.199 1.13l-14 30c-0.167 0.358-0.524 0.577-0.906 0.577zM5.508 14h11.492c0.552 0 1 0.448 1 1v11.492l10.931-23.423-23.423 10.931z"></path>';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        alert('Kan locatie niet ophalen: toestemming hiervoor is vereist.');
                    break;
                    default:
                        alert('Kan locatie niet ophalen: probeer het nog eens.');
                    break;
                }
            }, {enableHighAccuracy: true});
        },
        'toggle-user-menu': function() {
            this.classList.toggle('is-active');
            this.parentNode.parentNode.classList.toggle('is-active');
        },
        'reset-ticket': function(ev) {
            var form = document.querySelector('#section-' + this.getAttribute('href').replace('#', '')).parentNode;
            form.reset();
            form.elements['notitie_form[onderneming]'].value = '';
            form.elements['notitie_form[straat]'].value = '';
            form.elements['notitie_form[huisnummer]'].value = '';
            form.elements['notitie_form[geo]'].value = '';
            cs.utils.clearPreviews(document.getElementById('ticket-foto'));
            document.querySelector('#onderneming span').innerHTML = 'Ondernemer toevoegen';
            document.querySelector('#locatie span').innerHTML = 'Locatie';
            document.getElementById('map-container').classList.add('is-inactive');
        },
        'init-picker': function() {
            var form;
            if (this.getAttribute('href')) {
                form = document.querySelector('#section-' + this.getAttribute('href').replace('#', '')).parentNode;
            } else if (this.getAttribute('data-href')) {
                form = document.querySelector('#section-' + this.getAttribute('data-href')).parentNode;
            } else {
                return;
            }
            setTimeout(function() {
                cs.run(form);
                document.querySelector('[data-handler="use-my-location"]').click();
            }, 200);
        },
        'focus-first-field': function() {
            var form = document.querySelector('#section-' + this.getAttribute('href').replace('#', ''));
            setTimeout(function() {
                form.querySelector('input, textarea, select').focus();
            }, 200);
        },
        'confirm': function(ev) {
            var text = 'Zeker weten?';
            if (this.getAttribute('data-confirm')) {
                text = this.getAttribute('data-confirm');
            }
            if (!confirm(text)) {
                ev.preventDefault();
            }
        },
        'open-action-sheet': function() {
            this.nextElementSibling.classList.add('is-active');
        },
        'close-action-sheet': function() {
            document.querySelector('.action-sheet.is-active').classList.remove('is-active');
        },
        'selecteer-dienst': function() {
            cs.utils.xhr({
                url: this.getAttribute('data-url'),
                method: 'POST',
                params: 'telefoonboekEntry=' + this.getAttribute('data-telefoonboekentry-id'),
                isFormData: true,
                success: function(req) {
                    var dummy = document.createElement('div');
                    dummy.innerHTML = req.responseText;
                    document.querySelector('.tickets').innerHTML = dummy.querySelector('.tickets').innerHTML;
                }
            });
            location.href = '#';
        }
    },
    decorators: {
        'init-app': function() {
            this.addEventListener('click', function(ev) {
                if (ev.target.hasAttribute('data-handler')) {
                    var handlers = ev.target.getAttribute('data-handler').split(' ');
                    for (var i = 0; i < handlers.length; i++) {
                        if (handlers[i] && cs.handlers[handlers[i]]) {
                            cs.handlers[handlers[i]].call(ev.target, ev);
                        }
                    }
                }
            });
        },
        'store-id': function() {
            var id = this.getAttribute('data-id');
            if (window.localStorage && (!localStorage['last-id'] || (localStorage['last-id'] && parseInt(localStorage['last-id']) < id)) && location.hash == '') {
                localStorage['last-id'] = id;
            }
        },
        'change-date': function() {
            var oldValue = this.value;
            this.addEventListener('blur', function() {
                if (this.value != oldValue) {
                    location.href = this.getAttribute('data-url').replace('DATUM', this.value);
                }
            });
        },
        'file-picker': function() {
            if (this._decorated) {
                return;
            }
            if (this.files && window.URL) {
                this.addEventListener('change', function() {
                    cs.utils.clearPreviews(this.parentNode.parentNode);
                    var i, file, container = this.parentNode.parentNode;
                    for (i = 0; i < this.files.length; i++) {
                        file = this.files[i];
                        setTimeout(function() {
                            var img = document.createElement('img');
                            img.setAttribute('alt', '');
                            img.className = 'preview';
                            container.appendChild(img);
                            img.src = window.URL.createObjectURL(file);
                        }, i * 150);
                    }
                });
            }
            this._decorated = true;
        },
        'search-filter': function() {
            var items = this.parentNode.nextElementSibling.querySelectorAll('li');
            this.addEventListener('input', function() {
                var search = this.value.toLowerCase();
                cs.utils.forEach(items, function(index, item) {
                    if (item.textContent.toLowerCase().indexOf(search) !== -1) {
                        item.classList.remove('is-filtered');
                    } else {
                        item.classList.add('is-filtered');
                    }
                });
            });
        },
        'auto-refresh': function() {
            if (!window.localStorage) {
                return;
            }
            var link = this;
            var url = link.getAttribute('data-url').replace('{gebiedId}', link.getAttribute('data-gebied'));
            var counter = document.createElement('em');
            var first = true;
            var refresh = function() {
                if (!localStorage['last-id']) {
                    return;
                }
                cs.utils.xhr({
                    url: url.replace('{last}', localStorage['last-id']),
                    method: 'GET',
                    success: function(req) {
                        if (parseInt(req.responseText) !== 0) {
                            if (first) {
                                link.appendChild(counter);
                                first = false;
                            }
                            counter.textContent = parseInt(req.responseText);
                        }
                        setTimeout(refresh, 30000);
                    }
                });
            }
            setTimeout(refresh, 30000);
        },
        'map': function() {
            var container = this;
            var interactive = this.hasAttribute('data-interactive');
            var center = [52.373290, 4.893465];
            var point;
            var streetInput = container.getAttribute('data-street') && document.getElementById(container.getAttribute('data-street'));
            if (this.hasAttribute('data-center')) {
                point = this.getAttribute('data-center').substring(16);
                center = point.substring(0, point.length -1).split(' ');
            }
            requestAnimationFrame(function() {
                if (cs.map) {
                    cs.map.invalidateSize();
                } else {
                    cs.map = L.map(container, {
                        center: center,
                        zoom: 16,
                        attributionControl: false,
                        zoomControl: interactive,
                        dragging: interactive,
                        doubleClickZoom: interactive,
                        layers: [
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                minZoom: 14,
                                maxZoom: 19
                            })
                        ]
                    });
                    cs.map.on('moveend', function() {
                        var center = cs.map.getCenter();
                        var locatie = document.getElementById('locatie');
                        if (locatie) {
                            locatie.previousElementSibling.value = 'POINT(' + center.lat + ' ' + center.lng + ')';
                        }
                        if (!cs.mapAutoMove) {
                            cs.utils.fetchStreet(center.lat, center.lng, function(street) {
                                if (streetInput) {
                                    streetInput.value = street;
                                }
                                if (locatie) {
                                    locatie.querySelector('span').innerHTML = street;
                                }
                            });
                        } else {
                            setTimeout(function() {
                                cs.mapAutoMove = false;
                            }, 500);
                        }
                    });
                }
            });
        }
    },
    utils: {
        fetchStreet: function(lat, lon, callback) {
            cs.utils.xhr({
                url: GEOCODER + '/reverse.php?format=json&lat=' + lat + '&lon=' + lon + '&zoom=' + 21,
                timeout: 2000,
                success: function(request) {
                    var streets = [];
                    var response = JSON.parse(request.responseText);
                    if (response) {
                        streets.push(feature.properties.display_name);
                        if (streets.length) {
                            callback(streets[0])
                        }
                    }
                }
            });
        },
        xhr: function(options) {
            var request = new XMLHttpRequest();
            var params = options.params || null;
            var method = options.method || 'GET';
            var isFormData = options.isFormData || false;
            var errorFired = false;
            var error = function() {
                if (!errorFired && options.error) {
                    options.error(request);
                }
                errorFired = true;
            };
            request.open(method, options.url, true);
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            if (options.success) {
                request.onload = function() {
                    if ((options.xDomain && !request.status) || (options.success && (request.status >= 200 && request.status < 400))) {
                        options.success(request);
                    } else if (options.error && request.status >= 400) {
                        error();
                    }
                };
            }
            if (options.error) {
                request.onerror = function() {
                    error();
                };
                if (options.timeout) {
                    request.timeout = options.timeout;
                    request.ontimeout = function() {
                        error();
                    };
                } else if (method.toUpperCase() == 'POST') {
                    request.timeout = 10000;
                    request.ontimeout = function() {
                        error();
                    };
                }
            }
            if (isFormData == true) {
                request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            }
            request.send(params);
            return request;
        },
        clearPreviews: function(container) {
            var i, images = container.querySelectorAll('img');
            for (i = 0; i < images.length; i++) {
                images[i].parentNode.removeChild(images[i]);
            }
        },
        forEach: function(array, callback, scope) {
            for (var i = 0; i < array.length; i++) {
                callback.call(scope, i, array[i]);
            }
        }
    },
    run: function(context) {
        context = context || document;
        var i, j, decorators, element, elements = context.querySelectorAll('[data-decorator]');
        for (i = 0; i < elements.length; i++) {
            element = elements[i];
            decorators = element.getAttribute('data-decorator').split(/\s+/);
            for (j = 0; j < decorators.length; j++) {
                if (cs.decorators[decorators[j]]) {
                    cs.decorators[decorators[j]].call(element);
                } else {
                    console.log('Missing decorator: ' + decorators[j]);
                }
            }
        }
    }
};
cs.run();
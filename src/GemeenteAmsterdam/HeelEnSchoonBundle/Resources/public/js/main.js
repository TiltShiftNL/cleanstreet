var photo = document.getElementById('photo');
if (photo) {
    photo.onchange = function() {
        var file = this.files[0];
        var form = this.form;
        loadImage(file, function(img, data) {
            // document.getElementById('dummy').appendChild(img);
            form.classList.add('submitting');
            var lat = data.exif.get('GPSLatitude');
            var lon = data.exif.get('GPSLatitude');
            if (lat && lon) {
                //Convert coordinates to WGS84 decimal
                var latRef = data.exif.get('GPSLatitudeRef') || 'N';
                var lonRef = data.exif.get('GPSLongitudeRef') || 'W';
                lat = (lat[0] + lat[1]/60 + lat[2]/3600) * (latRef == 'N' ? 1 : -1);
                lon = (lon[0] + lon[1]/60 + lon[2]/3600) * (lonRef == 'W' ? -1 : 1);
                document.getElementById('coords').value = 'POINT(' + lat + ' ' + lon + ')';
                form.submit();
            } else {
                navigator.geolocation.getCurrentPosition(function(geo) {
                    document.getElementById('coords').value = 'POINT(' + geo.coords.latitude + ' ' + geo.coords.longitude + ')';
                    form.submit();
                }, function() {
                    form.submit();
                }, {
                    enableHighAccuracy: true
                });
            }
        }, {
            orientation: true
        });
    };
}
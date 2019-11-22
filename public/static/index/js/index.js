function pushNotice(notice){
    if (!("Notification" in window)) {

        console.log(notice);
    
    }else if (Notification.permission === "granted") {
    
        var notification = new Notification('PHP54+', {
            body: notice,
            icon: '/static/img/wechat-cheni.jpg'
        });
        
        // 点击跳转
        notification.onclick = function () {
            location = '/index.html';
            // 通过焦点事件，回到浏览器
            window.focus();
        }
        // 设置关闭时间
        setTimeout(()=>{
            notification.close();
        }, 3000);
    
    }else {
        Notification.requestPermission(function (permission) {
            if (permission === "granted") {
                var notification = new Notification('PHP54+', {
                    body: notice,
                    icon: '/static/img/wechat-cheni.jpg'
                });
                notification.onclick = function () {
                    location = '/index.html';
                    window.focus();
                }
                setTimeout(()=>{
                    notification.close();
                }, 3000);
            }else{
                console.log(notice);
            }
        });
    }
}

function geoFindMe() {
    if (!navigator.geolocation) {
        console.log("您的浏览器不支持地理位置");
        return;
    }

    function success(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        console.log('Latitude is ' + latitude + '° <br>Longitude is ' + longitude + '°');

        var img = new Image();
        img.src = "http://maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=13&size=300x300&sensor=false";

        // output.appendChild(img);
        console.log("img:" + img.src);
    };

    function error() {
        console.log("无法获取您的位置");
    };

    console.log("Locating…");

    navigator.geolocation.getCurrentPosition(success, error);
}

$(document).keydown(function (event) {
    switch (event.keyCode) {
        case 13:
            pushNotice(new Date().getTime());
            console.log(new Date().getTime());
            break;
        case 32:
            geoFindMe();
            break;
        default:
            console.log("按键：" + event.keyCode);
    }
});
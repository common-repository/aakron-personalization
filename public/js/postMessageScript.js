(function () {
    window.addEventListener("message", (event) => {
        if (event.origin !== "https://designtool.aakronline.com")
            return
        if (event.data) {
            let data = event.data;
            console.log(data);
            let imgEle = document.querySelector("#js_design_tool_frontend_area img");
            let fullImgUrl = data.imageUrl.find(img => img.indexOf("_fullImage") != -1);
            imgEle.src = fullImgUrl;
            let onlyCanvasImg = document.querySelector("#js_artwork_only_image");
            onlyCanvasImg.value =  data.canvasPdfUrl;
            let colorEle = document.querySelector("#js_artwork_only_color");
            colorEle.value =  data.color;
            let variantEle = document.querySelector("#js_artwork_only_imprint_location");
            variantEle.value =  data.variant;
            document.querySelector("#js_design_tool_frontend_area").style.display = "block";
            let iframe = document.querySelector("#iframeHolder iframe");
            //document.querySelector("#iframeHolder").removeChild(iframe);
            document.querySelector("#iframeHolder").style.display = "none";
        }
    }, false);
})()
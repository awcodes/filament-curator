import Cropper from "cropperjs";

export default function curation({statePath, fileName, fileType, presets = {}, checkCrossOrigin = true}) {
    return {
        statePath: statePath,
        filename: fileName,
        filetype: fileType,
        checkCrossOrigin: checkCrossOrigin,
        cropper: null,
        presets: presets,
        preset: 'custom',
        flippedHorizontally: false,
        flippedVertically: false,
        format: 'jpg',
        quality: 60,
        key: null,
        finalWidth: 0,
        finalHeight: 0,
        cropBoxData: {
            left: 0,
            top: 0,
            width: 0,
            height: 0,
        },
        data: {
            left: 0,
            top: 0,
            width: 0,
            height: 0,
            rotate: 0,
            scaleX: 1,
            scaleY: 1,
        },
        init() {
            this.destroy();

            setTimeout(() => {
                this.cropper = new Cropper(this.$refs.image, {
                    checkCrossOrigin: this.checkCrossOrigin,
                    background: false,
                });
            }, 100);

            this.$watch('preset', ($value) => {
                if ($value === 'custom') {
                    this.cropper.reset()
                    this.key = null;
                    this.format = 'jpg';
                    this.quality = 60;
                } else {
                    let containerData = this.cropper.getContainerData();
                    let cropBoxData = this.cropper.getCropBoxData();
                    let preset = this.presets.find((p) => p.key === $value)
                    let width = preset.width;
                    let height = preset.height;
                    let left = Math.round((containerData.width - width) / 2);
                    let top = Math.round((containerData.height - height) / 2);
                    this.cropper.setCropBoxData({...cropBoxData, left, top, width, height});
                    this.key = preset.key;
                    this.format = preset.format;
                    this.quality = preset.quality;
                }
            })
        },
        destroy() {
            if (this.cropper == null) return;
            this.cropper.destroy();
            this.cropper = null;
        },
        setData() {
            this.finalWidth = this.data.width;
            this.finalHeight = this.data.height;
            this.data = this.cropper.getData(true);
            this.cropBoxData = this.cropper.getCropBoxData();
        },
        updateData() {
            this.finalWidth = this.data.width;
            this.finalHeight = this.data.height;
            this.data = this.cropper.getData(true);
            this.cropBoxData = this.cropper.getCropBoxData();
        },
        setCropBoxX($event) {
            let currentCropBox = this.cropper.getCropBoxData();
            this.cropper.setCropBoxData({...currentCropBox, left: parseInt($event.target.value)})
        },
        setCropBoxY($event) {
            let currentCropBox = this.cropper.getCropBoxData();
            this.cropper.setCropBoxData({...currentCropBox, top: parseInt($event.target.value)})
        },
        setCropBoxWidth($event) {
            let currentCropBox = this.cropper.getCropBoxData();
            this.cropper.setCropBoxData({...currentCropBox, width: parseInt($event.target.value)})
        },
        setCropBoxHeight($event) {
            let currentCropBox = this.cropper.getCropBoxData();
            this.cropper.setCropBoxData({...currentCropBox, height: parseInt($event.target.value)})
        },
        flipHorizontally() {
            this.cropper.scaleY(this.flippedHorizontally ? 1 : -1);
            this.flippedHorizontally = !this.flippedHorizontally
        },
        flipVertically() {
            this.cropper.scaleX(this.flippedVertically ? 1 : -1);
            this.flippedVertically = !this.flippedVertically
        },
        saveCuration() {
            let data = this.cropper.getData(true);
            data = {
                ...data,
                containerData: this.cropper.getContainerData(),
                imageData: this.cropper.getImageData(),
                canvasData: this.cropper.getCanvasData(),
                croppedCanvasData: this.cropper.getCroppedCanvas(),
                format: this.format,
                quality: this.quality,
                preset: this.preset,
                key: this.key ?? this.preset,
            }
            this.$wire.saveCuration(data);
        },
    };
};
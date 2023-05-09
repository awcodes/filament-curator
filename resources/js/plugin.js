import Cropper from 'cropperjs'

document.addEventListener("alpine:init", () => {
    Alpine.data('curator', ({
        statePath,
        types,
        initialSelection = null
    }) => ({
        statePath,
        types,
        selected: [],
        files: [],
        nextPageUrl: null,
        isFetching: false,
        showEditForm: false,
        showUploadForm: true,
        async init() {
            await this.getFiles('/curator/media', initialSelection);
            const observer = new IntersectionObserver(
                ([e]) => {
                    if (e.isIntersecting) {
                        this.loadMoreFiles();
                    }
                },
                {
                    rootMargin: '0px',
                    threshold: [0],
                }
            );
            observer.observe(this.$refs.loadMore);

            this.$watch('selected', (value) => {
                if (value.length === 1) {
                    this.$wire.setSelection(value);
                    this.showEditForm = true;
                    this.showUploadForm = false;
                } else if (value.length > 1) {
                    this.showEditForm = false;
                    this.showUploadForm = false;
                } else {
                    this.showEditForm = false;
                    this.showUploadForm = true;
                }
            });

            if (initialSelection?.length > 0) {
                this.selected = initialSelection;
            }
        },
        getFiles: async function(url = '/curator/media', selected = []) {
            if (selected.length > 0) {
                let indicator = url.includes('?') ? '&' : '?';
                url = url + indicator + 'media=' + selected.map(obj => obj.id).join(',');
            }
            this.isFetching = true;
            const response = await fetch(url);
            const result = await response.json();
            this.files = this.files ? this.files.concat(result.data) : result.data;
            this.nextPageUrl = result.next_page_url;
            this.isFetching = false;
        },
        loadMoreFiles: async function() {
            if (this.nextPageUrl) {
                this.isFetching = true;
                await this.getFiles(this.nextPageUrl, this.selected?.id);
                this.isFetching = false;
            }
        },
        searchFiles: async function(event) {
            this.isFetching = true;
            const response = await fetch('/curator/media/search?q=' + event.target.value);
            const result = await response.json();
            this.files = result.data;
            this.isFetching = false;
        },
        addNewFile: function(media = null) {
            if (media) {
                this.files = [...media, ...this.files];
                this.$nextTick(() => {
                    this.addToSelection(media[0].id);
                })
            }
        },
        removeFile: function(media = null) {
            if (media) {
                this.files = this.files.filter((obj) => obj.id !== media.id);
                this.removeFromSelection(media.id);
            }
        },
        addToSelection: function(mediaId = null) {
            this.selected.push(this.files.find(obj => obj.id === mediaId));
        },
        removeFromSelection: function(mediaId = null) {
            this.selected = this.selected.filter((obj) => obj.id !== mediaId);
        },
        isSelected: function(mediaId = null) {
            if (this.selected.length === 0) return false;

            return this.selected.find((obj) => obj.id === mediaId) !== undefined;
        },
        insertMedia: function() {
            this.$dispatch('insert-media', {
                statePath: this.statePath,
                media: this.selected,
            });
        }
    }));

    Alpine.data('curation', ({ statePath, fileName, fileType, presets = {}}) => ({
        statePath: statePath,
        filename: fileName,
        filetype: fileType,
        cropper: null,
        presets: presets,
        preset: 'custom',
        flippedHorizontally: false,
        flippedVertically: false,
        format: 'jpg',
        quality:  60,
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
        flipHorizontally()
        {
            this.cropper.scaleX(this.flippedHorizontally ? 1 : -1);
            this.flippedHorizontally = ! this.flippedHorizontally
        },
        flipVertically()
        {
            this.cropper.scaleY(this.flippedVertically ? 1 : -1);
            this.flippedVertically = ! this.flippedVertically
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
    }));
})

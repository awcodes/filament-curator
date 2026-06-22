import Cropper from 'cropperjs'

export default function curation({
    statePath,
    fileName,
    fileType,
    presets = {},
}) {
    return {
        statePath: statePath,
        filename: fileName,
        filetype: fileType,
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
        currentRotation: 0,
        aspectRatio: null,
        cropBoxData: {
            x: 0,
            y: 0,
            width: 0,
            height: 0,
        },
        data: {
            x: 0,
            y: 0,
            width: 0,
            height: 0,
            rotate: 0,
        },
        init() {
            this.destroy()

            setTimeout(() => {
                this.cropper = new Cropper(this.$refs.image)

                const selection = this.cropper.getCropperSelection()
                const image = this.cropper.getCropperImage()

                image.$ready().then(() => {
                    this.setData()
                })

                selection.addEventListener('change', () => {
                    this.updateData()
                })

                window.addEventListener('add-curation', () => {
                    this.close()
                })
            }, 100)

            this.$watch('preset', ($value) => {
                if ($value === 'custom') {
                    this.cropper.getCropperSelection().$reset()
                    this.aspectRatio = null
                    this.key = null
                    this.format = 'jpg'
                    this.quality = 60
                } else {
                    const canvas = this.cropper.getCropperCanvas()
                    const selection = this.cropper.getCropperSelection()
                    const preset = this.presets.find((p) => p.key === $value)
                    const width = preset.width
                    const height = preset.height
                    const x = Math.round((canvas.offsetWidth - width) / 2)
                    const y = Math.round((canvas.offsetHeight - height) / 2)
                    selection.$change(x, y, width, height)
                    this.key = preset.key
                    this.format = preset.format
                    this.quality = preset.quality
                }
            })
        },
        destroy() {
            if (this.cropper == null) return
            this.cropper.destroy()
            this.cropper = null
        },
        setData() {
            const selection = this.cropper.getCropperSelection()
            this.data = {
                x: Math.round(selection.x),
                y: Math.round(selection.y),
                width: Math.round(selection.width),
                height: Math.round(selection.height),
                rotate: this.currentRotation,
            }
            this.cropBoxData = {
                x: Math.round(selection.x),
                y: Math.round(selection.y),
                width: Math.round(selection.width),
                height: Math.round(selection.height),
            }
            this.finalWidth = Math.round(selection.width)
            this.finalHeight = Math.round(selection.height)
        },
        updateData() {
            this.setData()
        },
        setCropBoxX($event) {
            const selection = this.cropper.getCropperSelection()
            selection.$change(
                parseInt($event.target.value),
                selection.y,
                selection.width,
                selection.height,
            )
        },
        setCropBoxY($event) {
            const selection = this.cropper.getCropperSelection()
            selection.$change(
                selection.x,
                parseInt($event.target.value),
                selection.width,
                selection.height,
            )
        },
        setCropBoxWidth($event) {
            const selection = this.cropper.getCropperSelection()
            selection.$change(
                selection.x,
                selection.y,
                parseInt($event.target.value),
                selection.height,
            )
        },
        setCropBoxHeight($event) {
            const selection = this.cropper.getCropperSelection()
            selection.$change(
                selection.x,
                selection.y,
                selection.width,
                parseInt($event.target.value),
            )
        },
        flipHorizontally() {
            this.flippedHorizontally = !this.flippedHorizontally
            this.cropper.getCropperImage().$scale(1, -1)
        },
        flipVertically() {
            this.flippedVertically = !this.flippedVertically
            this.cropper.getCropperImage().$scale(-1, 1)
        },
        zoom(ratio) {
            this.cropper.getCropperImage().$zoom(ratio)
        },
        rotateTo(angle) {
            const degrees = parseFloat(angle) || 0
            const diff = degrees - this.currentRotation
            this.cropper.getCropperImage().$rotate(diff)
            this.currentRotation = degrees
            this.data.rotate = degrees
        },
        setAspectRatio(ratio) {
            if (this.aspectRatio === ratio) {
                this.aspectRatio = null
                this.cropper.getCropperSelection().aspectRatio = null
            } else {
                this.aspectRatio = ratio
                this.cropper.getCropperSelection().aspectRatio = ratio
            }
        },
        setDragMode() {
            // Drag mode is not supported in Cropper.js v2.
            // Move and select actions are handled by the web component handles.
        },
        reset() {
            this.destroy()
            this.init()
        },
        async saveCuration() {
            const selection = this.cropper.getCropperSelection()
            const cropperImage = this.cropper.getCropperImage()

            // Extract the isotropic scale factor from the transform matrix (works for
            // scale, rotation, flip, and zoom combinations). Invert it to convert the
            // selection from display coordinates to near-native image resolution.
            const [a, b] = cropperImage.$getTransform()
            const imageScale = Math.sqrt(a * a + b * b)
            const scaleToNative = imageScale > 0 ? 1 / imageScale : 1

            const outputWidth = Math.max(
                1,
                Math.round(selection.width * scaleToNative),
            )
            const outputHeight = Math.max(
                1,
                Math.round(selection.height * scaleToNative),
            )

            const exportCanvas = await selection.$toCanvas({
                width: outputWidth,
                height: outputHeight,
            })

            const mimeType = `image/${this.format === 'jpg' ? 'jpeg' : this.format}`
            const dataUrl = exportCanvas.toDataURL(mimeType, this.quality / 100)

            this.$wire.saveCuration({
                dataUrl: dataUrl,
                width: outputWidth,
                height: outputHeight,
                format: this.format,
                quality: this.quality,
                preset: this.preset,
                key: this.key ?? this.preset,
            })
        },
    }
}

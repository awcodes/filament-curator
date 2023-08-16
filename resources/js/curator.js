export default function curator({statePath, types, initialSelection = null, isMultiple = false, directory = null}) {
    return {
        statePath,
        types,
        isMultiple,
        directory,
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
                    this.$wire.setSelection([]);
                    this.showEditForm = false;
                    this.showUploadForm = false;
                } else {
                    this.$wire.setSelection([]);
                    this.showEditForm = false;
                    this.showUploadForm = true;
                }
            });

            if (initialSelection?.length > 0) {
                this.selected = initialSelection;
            }
        },
        getIndicator: function (url) {
            return url.includes('?') ? '&' : '?';
        },
        getFiles: async function (url = '/curator/media', selected = []) {
            this.$nextTick(async () => {
                if (selected.length > 0) {
                    url = url + this.getIndicator(url) + 'media=' + selected.map(obj => obj.id).join(',');
                }
                if (this.directory) {
                    url = url + this.getIndicator(url) + 'directory=' + this.directory;
                }
                if (this.types) {
                    url = url + this.getIndicator(url) + 'types=' + this.types.join(',');
                }
                this.isFetching = true;
                const response = await fetch(url);
                const result = await response.json();
                this.files = this.files ? this.files.concat(result.data) : result.data;
                this.nextPageUrl = result.next_page_url;
                this.isFetching = false;
            });
        },
        loadMoreFiles: async function () {
            if (this.nextPageUrl) {
                this.isFetching = true;
                await this.getFiles(this.nextPageUrl, this.selected?.id);
                this.isFetching = false;
            }
        },
        searchFiles: async function (event) {
            this.isFetching = true;
            let url = '/curator/media/search?q=' + event.target.value;
            if (this.directory) {
                let indicator = url.includes('?') ? '&' : '?';
                url = url + indicator + 'directory=' + this.directory;
            }
            const response = await fetch(url);
            const result = await response.json();
            this.files = result.data;
            this.isFetching = false;
        },
        addNewFile: function (media = null) {
            if (media) {
                this.files = [...media, ...this.files];
                this.$nextTick(() => {
                    this.addToSelection(media[0].id);
                })
            }
        },
        removeFile: function (media = null) {
            if (media) {
                this.files = this.files.filter(obj => obj.id != media.id);
                this.removeFromSelection(media.id);
            }
        },
        addToSelection: function (mediaId = null, event = null) {
            if (this.selected.length === 1 && !this.isMultiple) {
                this.selected = [this.files.find(obj => obj.id == mediaId)];
                return;
            }

            if (event && event.metaKey) {
                this.selected.push(this.files.find(obj => obj.id == mediaId));
                return;
            }

            this.selected = [this.files.find(obj => obj.id == mediaId)];
        },
        removeFromSelection: function (mediaId = null) {
            this.selected = this.selected.filter(obj => obj.id != mediaId);
        },
        isSelected: function (mediaId = null) {
            if (this.selected.length === 0) return false;

            return this.selected.find((obj) => obj.id == mediaId) !== undefined;
        },
        insertMedia: function () {
            this.$dispatch('insert-media', {
                statePath: this.statePath,
                media: this.selected,
            });
        }
    }
};
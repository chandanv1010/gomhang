(function ($) {
    "use strict";

    const Compare = {
        selectors: {
            wrapper: '.compare-table-wrapper',
            countText: '.compare-total-text',
            modal: '#compare-modal',
            searchInput: '#compare-search-input',
            searchResults: '.compare-search-results',
        },
        currentPosition: null,
        modalInstance: null,
        searchTimer: null,

        init() {
            if (!$(this.selectors.wrapper).length) {
                return;
            }

            if (typeof UIkit !== 'undefined' && typeof UIkit.modal === 'function') {
                this.modalInstance = UIkit.modal(this.selectors.modal);
            }

            this.bindEvents();
            this.fetchProducts();
        },

        bindEvents() {
            const self = this;

            $(document).on('click', '.open-compare-modal', function (e) {
                e.preventDefault();
                self.currentPosition = parseInt($(this).data('position'), 10);
                $(self.selectors.searchInput).val('');
                self.fetchProducts();
                self.openModal();
            });

            $(document).on('keyup', self.selectors.searchInput, function () {
                clearTimeout(self.searchTimer);
                const keyword = $(this).val();
                self.searchTimer = setTimeout(() => {
                    self.fetchProducts(keyword);
                }, 300);
            });

            $(document).on('click', '.compare-search-option', function (e) {
                e.preventDefault();
                const productId = $(this).data('id');
                if (self.currentPosition === null) {
                    self.notify('Vui lòng chọn vị trí cần so sánh', 'warning');
                    return;
                }
                self.addProduct(productId, self.currentPosition);
            });

            $(document).on('click', '.compare-remove', function (e) {
                e.preventDefault();
                const rowId = $(this).data('rowid');
                const position = $(this).data('position');
                self.removeProduct({ rowId, position });
            });
        },

        openModal() {
            if (this.modalInstance) {
                this.modalInstance.show();
            } else {
                $(this.selectors.modal).addClass('uk-open');
            }
        },

        closeModal() {
            if (this.modalInstance) {
                this.modalInstance.hide();
            } else {
                $(this.selectors.modal).removeClass('uk-open');
            }
        },

        fetchProducts(keyword = '') {
            const wrapper = $(this.selectors.wrapper);
            const url = wrapper.data('compare-search');
            if (!url) return;

            $.ajax({
                url,
                type: 'GET',
                data: { keyword },
                success: (res) => {
                    this.renderProductList(res.data || []);
                },
                error: () => {
                    this.renderProductList([]);
                }
            });
        },

        renderProductList(items) {
            const container = $(this.selectors.searchResults);
            container.empty();

            if (!items.length) {
                container.html(`<div class="compare-search-placeholder">${container.data('empty')}</div>`);
                return;
            }

            items.forEach(item => {
                const html = `
                    <button class="compare-search-option" data-id="${item.id}">
                        <span class="compare-search-thumb image img-cover">
                            <img src="${item.image}" alt="${item.name}" width="120px" class="object-containt">
                        </span>
                        <span class="compare-search-content">
                            <span class="compare-search-title">${item.name}</span>
                            <span class="compare-search-meta">Mã: ${item.code || '-'} • Giá: ${item.price}</span>
                        </span>
                    </button>
                `;
                container.append(html);
            });
        },

        addProduct(productId, position) {
            const wrapper = $(this.selectors.wrapper);
            const url = wrapper.data('compare-add');
            if (!url) return;

            $.ajax({
                url,
                type: 'POST',
                data: {
                    id: productId,
                    position,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: (res) => {
                    if (res.code === 1) {
                        this.updateTable(res);
                        this.notify(res.message, 'success');
                        this.closeModal();
                    } else {
                        this.notify(res.message || 'Không thể thêm sản phẩm', 'warning');
                    }
                },
                error: () => {
                    this.notify('Không thể thêm sản phẩm, vui lòng thử lại', 'error');
                }
            });
        },

        removeProduct(payload) {
            const wrapper = $(this.selectors.wrapper);
            const url = wrapper.data('compare-remove');
            if (!url) return;

            $.ajax({
                url,
                type: 'POST',
                data: Object.assign({}, payload, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }),
                success: (res) => {
                    if (res.code === 1) {
                        this.updateTable(res);
                        this.notify(res.message, 'success');
                    } else {
                        this.notify(res.message || 'Không thể xóa sản phẩm', 'warning');
                    }
                },
                error: () => {
                    this.notify('Không thể xóa sản phẩm, vui lòng thử lại', 'error');
                }
            });
        },

        updateTable(res) {
            if (res.html) {
                $(this.selectors.wrapper).html(res.html);
            }
            if (typeof res.compareTotal !== 'undefined') {
                $(this.selectors.countText).text(res.compareTotal);
                $('.compare-count').text(res.compareTotal);
            }
        },

        notify(message, type = 'info') {
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            } else {
                alert(message);
            }
        }
    };

    $(document).ready(function () {
        Compare.init();
    });
})(jQuery);


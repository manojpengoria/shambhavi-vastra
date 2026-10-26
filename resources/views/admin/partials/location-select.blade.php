{{--
    Searchable AJAX dropdowns for location masters.
    Usage: <select class="js-location-select" data-lookup-url="..." data-placeholder="..."
        [data-include-disabled="1"] [data-depends-on="#parent_select" data-depends-param="city_id"]>
--}}
@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('.js-location-select').each(function () {
                    const select = $(this);
                    const parentSelect = select.data('depends-on') ? $(select.data('depends-on')) : null;

                    select.select2({
                        theme: 'bootstrap',
                        width: '100%',
                        placeholder: select.data('placeholder'),
                        allowClear: true,
                        ajax: {
                            url: select.data('lookup-url'),
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                const query = {
                                    q: params.term || '',
                                    page: params.page || 1,
                                };

                                if (select.data('include-disabled')) {
                                    query.include_disabled = 1;
                                }

                                if (parentSelect) {
                                    query[select.data('depends-param')] = parentSelect.val();
                                }

                                return query;
                            },
                        },
                    });

                    if (parentSelect) {
                        const syncWithParent = function () {
                            select.prop('disabled', !parentSelect.val());
                        };

                        parentSelect.on('change', function () {
                            select.val(null).trigger('change');
                            syncWithParent();
                        });

                        syncWithParent();
                    }
                });
            });
        </script>
    @endpush
@endonce

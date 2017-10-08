<div class="col-lg-3 order-lg-1 g-brd-right--lg g-brd-gray-light-v4 g-mb-80">
    <div class="g-pr-20--lg">

        <div class="g-md-55">
            <a class="btn btn-outline-secondary g-mr-10 g-mb-50"
               href="#">Neue Transaktion</a>
        </div>

        <!-- Links -->
        <div class="g-mb-50">
            <h3 class="h5 g-color-black g-font-weight-600 mb-4">Links</h3>
            <ul class="list-unstyled g-font-size-13 mb-0">
                <li>
                    <a class="d-block u-link-v5 g-color-gray-dark-v4 rounded g-px-20 g-py-8 {{ active_class(if_route('portfolios.show')) }}"
                       href="{{ route('portfolios.show', $portfolio->slug) }}">
                        <i class="mr-2 fa fa-angle-right"></i>@lang('navigation.dashboard')
                    </a>
                </li>
                <li>
                    <a class="d-block u-link-v5 g-color-gray-dark-v4 rounded g-px-20 g-py-8 {{ active_class(if_route_pattern(['transactions.*'])) }}"
                       href="{{ route('transactions.index', $portfolio->slug) }}">
                        <i class="mr-2 fa fa-angle-right"></i>@lang('navigation.transactions')
                    </a>
                </li>
                <li>
                    <a class="d-block u-link-v5 g-color-gray-dark-v4 rounded g-px-20 g-py-8 {{ active_class(if_route_pattern(['positions.*', 'assets.*'])) }}"
                       href="{{ route('positions.index', $portfolio->slug) }}">
                        <i class="mr-2 fa fa-angle-right"></i>@lang('navigation.positions')
                    </a>
                </li>
                <li>
                    <a class="d-block u-link-v5 g-color-gray-dark-v4 rounded g-px-20 g-py-8"
                       href="{{ route('home.coming') }}">
                        <i class="mr-2 fa fa-angle-right"></i>@lang('navigation.optimize')</a>
                </li>
                <li>
                    <a class="d-block u-link-v5 g-color-gray-dark-v4 rounded g-px-20 g-py-8 {{ active_class(if_route('portfolios.edit')) }}"
                       href="{{ route('portfolios.edit', ['slug' => $portfolio->slug]) }}">
                        <i class="mr-2 fa fa-angle-right"></i>@lang('navigation.settings.title')</a>
                </li>
            </ul>
        </div>
    </div>
</div>
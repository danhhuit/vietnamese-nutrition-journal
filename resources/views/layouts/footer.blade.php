<footer class="border-t border-emerald-900 bg-[#1f6f22] py-10 text-sm text-emerald-50 dark:bg-emerald-950 dark:text-emerald-100">
    <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:grid-cols-3 sm:px-6 lg:px-8">
        <div><strong class="text-white">{{ __('Dinh dưỡng Việt') }}</strong><p class="mt-2">{{ __('Tra cứu và ghi chép dinh dưỡng món Việt. Thông tin chỉ để tham khảo.') }}</p></div>
        <div><strong class="text-white">{{ __('Điều hướng') }}</strong><div class="mt-2 grid gap-2"><a href="{{ route('foods.index') }}">{{ __('Thực phẩm') }}</a><a href="{{ route('restaurants.index') }}">{{ __('Quán ăn') }}</a><a href="{{ route('about') }}">{{ __('Nguồn dữ liệu') }}</a></div></div>
        <div><strong class="text-white">{{ __('Nguồn tham khảo') }}</strong><div class="mt-2 grid gap-2"><a href="https://www.fao.org/fileadmin/templates/food_composition/documents/pdf/VTN_FCT_2007.pdf" target="_blank" rel="noopener">Bảng thành phần thực phẩm Việt Nam 2007</a><a href="https://viendinhduong.vn/vi/cong-cu-va-tien-ich/gia-tri-dinh-duong-thuc-pham" target="_blank" rel="noopener">Viện Dinh dưỡng Quốc gia</a></div></div>
    </div>
</footer>

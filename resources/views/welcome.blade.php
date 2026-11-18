<x-app-layout>
    @include('home.slider')
    <form action="{{ route('search') }}" class="card mt-8"><label for="q" class="label">Tìm món ăn, thực phẩm hoặc quán ăn</label><div class="flex flex-col gap-3 sm:flex-row"><input id="q" name="q" class="input" placeholder="Có thể nhập tên không dấu"><button class="btn">Tìm kiếm</button></div></form>
    <section class="mt-8 grid gap-4 md:grid-cols-3">
        <article class="card"><h2 class="text-xl font-semibold">Tra cứu rõ ràng</h2><p class="mt-2 text-zinc-600 dark:text-zinc-300">Thông số trên 100g, bảng thành phần và nguồn từng mục.</p></article>
        <article class="card"><h2 class="text-xl font-semibold">Nhật ký dễ dùng</h2><p class="mt-2 text-zinc-600 dark:text-zinc-300">Ghi từng bữa và nhìn ngay lượng năng lượng đã nạp.</p></article>
        <article class="card"><h2 class="text-xl font-semibold">Mục tiêu cá nhân</h2><p class="mt-2 text-zinc-600 dark:text-zinc-300">Theo dõi tiến độ với báo cáo tuần và biểu đồ.</p></article>
    </section>
</x-app-layout>

@extends('admin.layout')
@section('title', 'Category Details: ' . $category->name)

@section('content')
    <div class="max-w-4xl space-y-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.index') }}"
                    class="w-8 h-8 rounded-full border flex items-center justify-center text-xs hover:bg-stone-50">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="font-extrabold text-base text-stone-900">{{ $category->name }}</h2>
                    <span class="text-xs text-stone-400">Slug: {{ $category->slug }}</span>
                </div>
            </div>
            <a href="{{ route('admin.categories.edit', $category->id) }}"
                class="border border-[#b5122b] text-[#b5122b] px-4 py-2 rounded-xl text-xs font-bold hover:bg-rose-50">
                Edit Category
            </a>
        </div>

        <div class="bg-white border rounded-2xl p-6 shadow-sm flex items-center space-x-6">
            <img src="{{ $category->image ?? 'https://placehold.co/400x300' }}"
                class="w-32 h-24 object-cover rounded-xl border">
            <div>
                <span class="text-xs text-stone-400 font-bold uppercase tracking-wider">Total Products</span>
                <div class="text-2xl font-black text-stone-800">{{ $category->products->count() }} Items</div>
            </div>
        </div>

        <h3 class="font-bold text-xs uppercase tracking-wider text-stone-500">Products in this Category</h3>
        <div class="grid grid-cols-2 gap-4">
            @forelse($category->products as $p)
                <div class="bg-white border rounded-2xl p-4 flex justify-between items-center shadow-sm">
                    <div>
                        <h4 class="font-bold text-xs text-stone-900">{{ $p->name }}</h4>
                        <span class="text-xs text-[#b5122b] font-bold">
                            {{ $p->base_price !== null ? number_format($p->base_price, 3) . ' KD' : 'Price on selection' }}
                        </span>
                    </div>
                    <a href="{{ route('admin.products.show', $p->id) }}"
                        class="text-xs font-bold text-stone-500 hover:text-[#b5122b] underline">
                        View Product
                    </a>
                </div>
            @empty
                <p class="text-xs text-stone-400 col-span-2">No products linked to this category yet.</p>
            @endforelse
        </div>
    </div>
@endsection

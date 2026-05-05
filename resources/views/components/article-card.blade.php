<div class="bg-white rounded-2xl border border-forest-100 overflow-hidden shadow-sm hover:-translate-y-1 transition">

  <!-- Image -->
  <div class="aspect-video overflow-hidden">
    <img
      src="{{ $article->image }}"
      alt="{{ $article->title }}"
      class="w-full h-full object-cover"
    >
  </div>

  <!-- Content -->
  <div class="p-5">
    <div class="flex items-center space-x-2 mb-3">
      <span class="px-2 py-1 bg-forest-50 text-forest-600 text-[10px] font-bold rounded uppercase">
        {{ $article->category }}
      </span>
      <span class="text-[10px] text-gray-400 font-medium">
        {{ $article->date }}
      </span>
    </div>

    <h3 class="text-gray-900 font-bold leading-snug mb-2 hover:text-forest-600 transition-colors">
      {{ $article->title }}
    </h3>

    <p class="text-gray-500 text-xs line-clamp-2 leading-relaxed">
      {{ $article->excerpt }}
    </p>

    <a href="/article/{{ $article->id }}" 
       class="mt-4 inline-block text-xs font-bold text-forest-600 hover:text-forest-700">
      Baca Selengkapnya
    </a>
  </div>

</div>
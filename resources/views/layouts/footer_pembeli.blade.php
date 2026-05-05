<footer class="bg-forest-950 text-forest-100 pt-16 pb-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">

      <!-- Brand -->
      <div class="space-y-4">
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 bg-forest-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-lg">C</span>
          </div>
          <span class="text-xl font-bold text-white tracking-tight">Campify</span>
        </div>
        <p class="text-forest-200 text-sm leading-relaxed">
          Solusi perlengkapan camping premium untuk petualangan tak terlupakan. 
          Sewa atau beli, kami siap menemani perjalananmu.
        </p>
      </div>

      <!-- Quick Links -->
      <div>
        <h4 class="text-white font-semibold mb-4">Belanja</h4>
        <ul class="space-y-2 text-sm text-forest-300">
          <li><a href="/category/Tenda" class="hover:text-forest-100 transition-colors">Tenda</a></li>
          <li><a href="/category/Tas Gunung" class="hover:text-forest-100 transition-colors">Tas Gunung</a></li>
          <li><a href="/category/Alat Masak" class="hover:text-forest-100 transition-colors">Alat Masak</a></li>
          <li><a href="/category/Aksesoris" class="hover:text-forest-100 transition-colors">Aksesoris</a></li>
        </ul>
      </div>

      <!-- Support -->
      <div>
        <h4 class="text-white font-semibold mb-4">Dukungan</h4>
        <ul class="space-y-2 text-sm text-forest-300">
          <li><a href="/articles" class="hover:text-forest-100 transition-colors">Tips Camping</a></li>
          <li><a href="/faq" class="hover:text-forest-100 transition-colors">FAQ</a></li>
          <li><a href="/contact" class="hover:text-forest-100 transition-colors">Hubungi Kami</a></li>
          <li><a href="/terms" class="hover:text-forest-100 transition-colors">Syarat & Ketentuan</a></li>
        </ul>
      </div>

      <!-- Newsletter -->
      <div>
        <h4 class="text-white font-semibold mb-4">Newsletter</h4>
        <p class="text-xs text-forest-300 mb-4">Dapatkan info promo dan tips outdoor terbaru.</p>
        <form action="#" method="POST" class="flex space-x-2">
          @csrf
          <input
            type="email"
            name="email"
            placeholder="Email kamu"
            class="bg-forest-800 border border-forest-700 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-forest-500 w-full text-white placeholder-forest-300"
          />
          <button class="bg-forest-600 hover:bg-forest-500 text-white px-4 py-2 rounded-lg text-xs font-medium transition-colors">
            Gabung
          </button>
        </form>
      </div>

    </div>

    <div class="pt-8 border-t border-forest-800 flex flex-col md:flex-row justify-between items-center text-xs text-forest-400">
      <p>© {{ date('Y') }} Campify. All rights reserved.</p>
      <div class="flex space-x-6 mt-4 md:mt-0">
        <span>Instagram</span>
        <span>Youtube</span>
        <span>Twitter</span>
      </div>
    </div>
  </div>
</footer>
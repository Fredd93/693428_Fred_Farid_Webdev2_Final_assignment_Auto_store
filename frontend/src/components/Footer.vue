<template>
  <footer class="site-footer mt-16">
    <div class="site-footer-inner flex flex-col gap-3 px-1 py-8 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
      <div class="font-semibold text-slate-700">Grand Transmission Auto</div>
      <div class="flex gap-4">
        <RouterLink to="/home" class="hover:text-slate-900">Home</RouterLink>
        <RouterLink to="/cars" class="hover:text-slate-900">Inventory</RouterLink>
        <RouterLink to="/login" class="hover:text-slate-900">Login</RouterLink>
      </div>
      <div>© 2026 Grand Transmission Auto</div>
    </div>
  </footer>
</template>

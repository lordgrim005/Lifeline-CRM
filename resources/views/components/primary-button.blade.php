<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-gradient-to-br from-brand-600 to-purple-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:shadow-lg hover:shadow-brand-500/30 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-300']) }}>
    {{ $slot }}
</button>

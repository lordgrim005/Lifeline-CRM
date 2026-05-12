@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 bg-white text-gray-900 focus:border-brand-500 focus:ring-brand-500 rounded-xl shadow-sm dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:placeholder-slate-400 dark:focus:border-brand-400 dark:focus:ring-brand-400 transition-colors']) }}>

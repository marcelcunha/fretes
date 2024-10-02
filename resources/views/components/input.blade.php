<div class="">
    <label for="{{ $id }}" class="fi-input-wrp-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $label }}</label>

    <div class="mt-2 fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 fi-disabled bg-gray-50 dark:bg-transparent">
        <input type="text" {{ $attributes }} name="{{ $name }}"
            class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6">
    </div>
</div>

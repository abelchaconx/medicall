<aside
    class="fixed top-16 left-0 h-[calc(100vh-4rem)] w-64 bg-white dark:bg-gray-800 shadow dark:shadow-none border-r border-gray-200 dark:border-gray-700 z-40 transform transition-transform duration-200 ease-in-out lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="p-4 space-y-2">
        <nav>
           
            <a href="<?php echo e(route('users.index')); ?>"
               class="block px-4 py-2 rounded text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(request()->routeIs('users.*') ? 'bg-gray-200 dark:bg-gray-700 dark:text-gray-100 font-semibold' : ''); ?>">
                <span class="inline-flex items-center gap-2">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11c1.657 0 3-1.343 3-3S17.657 5 16 5s-3 1.343-3 3 1.343 3 3 3zM8 11c1.657 0 3-1.343 3-3S9.657 5 8 5 5 6.343 5 8s1.343 3 3 3zM8 14c-2.667 0-8 1.333-8 4v1h16v-1c0-2.667-5.333-4-8-4zM16 14c-.333 0-.667 0-1 .036" />
                    </svg>
                    Usuarios
                </span>
            </a>
            <a href="<?php echo e(route('roles.index')); ?>"
               class="block px-4 py-2 rounded text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(request()->routeIs('roles.*') ? 'bg-gray-200 dark:bg-gray-700 dark:text-gray-100 font-semibold' : ''); ?>">
                <span class="inline-flex items-center gap-2">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v4a1 1 0 001 1h3v2H5a1 1 0 000 2h14a1 1 0 000-2h-2v-2h3a1 1 0 001-1V7"/></svg>
                    Roles
                </span>
            </a>
            <a href="<?php echo e(route('permissions.index')); ?>"
               class="block px-4 py-2 rounded text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(request()->routeIs('permissions.*') ? 'bg-gray-200 dark:bg-gray-700 dark:text-gray-100 font-semibold' : ''); ?>">
                <span class="inline-flex items-center gap-2">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h6"/></svg>
                    Permisos
                </span>
            </a>
            
            
        </nav>
    </div>
</aside>
<?php /**PATH C:\laragon\www\medicall\resources\views/components/dashboard-menu.blade.php ENDPATH**/ ?>
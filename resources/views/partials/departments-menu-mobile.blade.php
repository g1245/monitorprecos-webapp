<!-- Mobile Departments Menu - Full Screen -->
<div id="mobileDepartmentsMenu" class="hidden fixed inset-0 bg-white z-50 md:hidden overflow-y-auto">
    <div class="flex flex-col h-full">
        <div class="bg-blue-800 text-white p-4 flex items-center justify-between">
            <h2 class="text-lg font-bold">Menu</h2>
            <button id="closeMobileMenu" class="text-white hover:text-blue-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex-1 p-4">
            <!-- Highlighted quick-access links -->
            <div class="mb-6">
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('stores.index') }}" class="flex items-center space-x-2 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-blue-800 font-semibold hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        <span>Lojas</span>
                    </a>
                    <a href="{{ route('destaques.index') }}" class="flex items-center space-x-2 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 text-yellow-800 font-semibold hover:bg-yellow-100 transition-colors">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <span>Destaques</span>
                    </a>
                    <a href="{{ route('pages.grupo') }}" class="flex items-center space-x-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-green-800 font-semibold hover:bg-green-100 transition-colors">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                        <span>Grupo WhatsApp</span>
                    </a>
                    <a href="{{ route('pages.how') }}" class="flex items-center space-x-2 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-blue-800 font-semibold hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Como Funciona</span>
                    </a>
                    <a href="{{ route('pages.about') }}" class="flex items-center space-x-2 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-blue-800 font-semibold hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Sobre Nós</span>
                    </a>
                </div>
            </div>

            <!-- Departments -->
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Departamentos</h3>
            </div>
            @foreach($departmentMenu as $parentDepartment)
                <div class="mb-6">
                    <h3 class="font-bold text-blue-800 mb-3 text-lg border-b border-gray-200 pb-2">
                        <a href="{{ route('department.index', ['alias' => Str::of($parentDepartment->name)->slug(), 'departmentId' => $parentDepartment->id]) }}" 
                        class="hover:text-blue-400 transition-colors">
                            {{ $parentDepartment->name }}
                        </a>
                    </h3>
                    <ul class="space-y-3">
                        @foreach($parentDepartment->children as $childDepartment)
                            <li>
                                <a href="{{ route('department.index', ['alias' => Str::of($childDepartment->name)->slug(), 'departmentId' => $childDepartment->id]) }}" 
                                class="text-gray-700 block py-1">
                                    {{ $childDepartment->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</div>
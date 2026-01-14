<x-app-layout>
    <h2 class="text-lg font-semibold mb-4">Manajemen Akses Menu</h2>

    <form action="{{ route('menu-management.store') }}" method="POST">
        @csrf

        <table class="table-auto w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">Menu</th>
                    @foreach($roles as $role)
                        <th class="px-4 py-2 border text-center">{{ strtolower($role->name) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($menus as $menu)
                    <tr>
                        <td class="border px-4 py-2">{{ $menu->name }}</td>
                        @foreach($roles as $role)
                            <td class="border px-4 py-2 text-center">
                                <input
                                    type="checkbox"
                                    name="access[{{ $menu->id }}][{{ $role->id }}]"
                                    {{ isset($roleMenuAccess[$menu->id][$role->id]) ? 'checked' : '' }}
                                >
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button
            type="submit"
            class="mt-4 px-4 py-2 bg-black text-white rounded hover:bg-gray-800 disabled:opacity-50"
        >Simpan Akses</button>
    </form>
</x-app-layout>

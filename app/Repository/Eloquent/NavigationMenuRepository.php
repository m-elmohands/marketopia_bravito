<?php

namespace App\Repository\Eloquent;

use App\Models\NavigationMenu;
use App\Repository\Contracts\NavigationMenuRepositoryInterface;

class NavigationMenuRepository implements NavigationMenuRepositoryInterface
{
    public function getAll()
    {
        return NavigationMenu::all();
    }

    public function getActive()
    {
        return NavigationMenu::active()->get();
    }

    public function getOrdered()
    {
        return NavigationMenu::active()->ordered()->get();
    }

    public function getByLocation($location)
    {
        return NavigationMenu::active()->byLocation($location)->ordered()->get();
    }

    public function getRootItems()
    {
        return NavigationMenu::active()->rootItems()->ordered()->get();
    }

    public function getMenuTree($location)
    {
        return NavigationMenu::active()
            ->byLocation($location)
            ->rootItems()
            ->ordered()
            ->with(['children' => function ($query) {
                $query->active()->ordered()->with('children');
            }])
            ->get();
    }

    public function create(array $data)
    {
        return NavigationMenu::create($data);
    }

    public function update($id, array $data)
    {
        $menu = NavigationMenu::findOrFail($id);
        $menu->update($data);
        return $menu;
    }

    public function delete($id)
    {
        $menu = NavigationMenu::findOrFail($id);
        return $menu->delete();
    }
}

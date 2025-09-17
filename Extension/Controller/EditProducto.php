<?php
/**
 * This file is part of Ubicaciones plugin for FacturaScripts.
 * FacturaScripts Copyright (C) 2015-2025 Carlos Garcia Gomez <carlos@facturascripts.com>
 * Ubicaciones    Copyright (C) 2019-2025 Jose Antonio Cuello Principal <yopli2000@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Plugins\Ubicaciones\Extension\Controller;

use Closure;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

/**
 * Controller to edit a single item from the Producto controller
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 *
 * @method addListView(string $viewName, string $model, string $title, string $icon): ListView
 * @method getModel(string $string): mixed
 */
class EditProducto
{
    /**
     * Load views
     */
    public function createViews(): Closure
    {
        return function (): void {
            $this->createViewVariantLocations();
        };
    }

    /**
     * Add and configure Variant Location list view
     */
    public function createViewVariantLocations(): Closure
    {
        return function($viewName = 'ListVariantLocation'): void {
            $this->addListView($viewName, 'Join\VariantLocation', 'locations', 'fa-solid fa-search-location')
                // Settings
                ->disableColumn('product')
                // Search and order
                ->addSearchFields(['aisle', 'rack', 'shelf', 'drawer'])
                ->addOrderBy(['codewarehouse', 'reference'], 'reference', 1)
                ->addOrderBy(['codewarehouse', 'aisle', 'rack', 'shelf', 'drawer'], 'warehouse')
                ->addOrderBy(['aisle', 'rack', 'shelf', 'drawer', 'codewarehouse'], 'location');
        };
    }

    /**
     * Load view data procedure
     */
    public function loadData(): Closure
    {
        return function($viewName, $view): void {
            if ($viewName == 'ListVariantLocation') {
                $where = [new DataBaseWhere('idproduct', $this->getModel('idproducto'))];
                $view->loadData('', $where);
            }
        };
    }
}

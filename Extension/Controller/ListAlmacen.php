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
use FacturaScripts\Core\Model\CodeModel;

/**
 *  Controller to list the items in the list warehouse controller
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 *
 * @method addView(string $viewName, string $model, string $title, string $icon): ListView
 */
class ListAlmacen
{
    /**
     * Load views
     */
    public function createViews(): Closure
    {
        return function(): void {
            $this->createViewLocations();
        };
    }

    /**
     * Add and configure Location list view
     */
    public function createViewLocations(): Closure
    {
        return function($viewName = 'ListLocation'): void {
            $this->addView($viewName, 'Location', 'locations', 'fa-solid fa-map-marker-alt')
                ->addSearchFields(['aisle', 'rack', 'shelf', 'drawer'])
                ->addOrderBy(['codewarehouse', 'aisle', 'rack', 'shelf', 'drawer'], 'warehouse')
                ->addOrderBy(['aisle', 'rack', 'shelf', 'drawer', 'codewarehouse'], 'aisle')
                ->addFilterSelect('warehouse', 'warehouse', 'codewarehouse',
                    CodeModel::all('almacenes', 'codalmacen', 'nombre'))
                ->addFilterSelect('aisle', 'aisle', 'aisle',
                    CodeModel::all('locations', 'aisle', 'aisle'));
        };
    }
}

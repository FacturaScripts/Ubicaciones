<?php
/**
 * This file is part of Ubicaciones plugin for FacturaScripts.
 * FacturaScripts Copyright (C) 2015-2022 Carlos Garcia Gomez <carlos@facturascripts.com>
 * Ubicaciones    Copyright (C) 2019-2024 Jose Antonio Cuello Principal <yopli2000@gmail.com>
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
use FacturaScripts\Core\Tools;
use FacturaScripts\Dinamic\Model\CodeModel;

/**
 * Controller to list the items in the List Product controller
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 *
 * @property CodeModel $codeModel
 * @method addView(string $viewName, string $model, string $title, string $icon)
 */
class ListProducto
{
    /**
     * Load views
     */
    public function createViews()
    {
        return function () {
            $this->createViewVariantLocations();
        };
    }

    /**
     * Add and configure Variant Location list view
     *
     * @return Closure
     */
    public function createViewVariantLocations(): Closure
    {
        return function ($viewName = 'ListVariantLocation') {
            $view = $this->addView($viewName, 'Join\VariantLocation', 'locations', 'fas fa-search-location')
                // SETTINGS
                ->setSettings($viewName, 'btnNew', false)
                ->setSettings($viewName, 'btnDelete', false)
                ->setSettings($viewName, 'checkBoxes', false)
                // SEARCH & ORDER
                ->addSearchFields(['aisle', 'rack', 'shelf', 'drawer'])
                ->addOrderBy(['codewarehouse', 'aisle', 'rack', 'shelf', 'drawer'], 'warehouse')
                ->addOrderBy(['aisle', 'rack', 'shelf', 'drawer', 'codewarehouse'], 'location');

            // FILTERS
            $warehouseValues = $this->codeModel->all('almacenes', 'codalmacen', 'nombre');
            $view->addFilterSelect('warehouse', 'warehouse', 'codewarehouse', $warehouseValues);

            $aisleValues = $this->codeModel->all('locations', 'aisle', 'aisle');
            $view->addFilterSelect('aisle', 'aisle', 'aisle', $aisleValues);

            $i18n = Tools::lang();
            $view->addFilterSelectWhere('type', [
                ['label' => $i18n->trans('type'), 'where' => []],
                ['label' => $i18n->trans('storage'), 'where' => [new DataBaseWhere('locations.storagetype', 0)]],
                ['label' => $i18n->trans('picking'), 'where' => [new DataBaseWhere('locations.storagetype', 1)]],
            ]);

            $view->addFilterSelectWhere('status', [
                ['label' => $i18n->trans('status'), 'where' => []],
                ['label' => $i18n->trans('not-blocked'), 'where' => [new DataBaseWhere('COALESCE(productos.bloqueado, 0)', 0)]],
                ['label' => $i18n->trans('blocked'), 'where' => [new DataBaseWhere('COALESCE(productos.bloqueado, 0)', 1)]],
            ]);

            $view->addFilterAutocomplete('product', 'product', 'productos.referencia', 'productos', 'referencia', 'descripcion');
            $view->addFilterAutocomplete('reference', 'reference', 'reference', 'Variante', 'referencia', 'referencia');
        };
    }
}

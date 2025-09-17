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
namespace FacturaScripts\Plugins\Ubicaciones\Controller;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Lib\ExtendedController\EditController;
use FacturaScripts\Core\Tools;
use FacturaScripts\Dinamic\Lib\ExtendedController\BaseView;
use FacturaScripts\Dinamic\Model\Producto;
use FacturaScripts\Dinamic\Model\Variante;
use FacturaScripts\Plugins\Ubicaciones\Model\Location;

/**
 * Controller to edit a single item from the Location model
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class EditVariantLocation extends EditController
{
    /**
     * Create the view to display.
     */
    protected function createViews()
    {
        parent::createViews();
        $this->setSettings($this->getMainViewName(), 'btnNew', false);
    }

    /**
     * Returns the model name
     */
    public function getModelClassName(): string
    {
        return 'VariantLocation';
    }

    /**
     * Returns basic page attributes
     *
     * @return array
     */
    public function getPageData(): array
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'variant-location';
        $pageData['menu'] = 'warehouse';
        $pageData['icon'] = 'fa-solid fa-search-location';
        return $pageData;
    }

    /**
     * Loads the data to display.
     *
     * @param string   $viewName
     * @param BaseView $view
     */
    protected function loadData($viewName, $view)
    {
        parent::loadData($viewName, $view);

        if ($viewName === $this->getMainViewName()) {
            $view->disableColumn('code');  // Force disable PK column
            $view->disableColumn('product-code');  // Force disable Link column with product

            // Load product, variant and location data
            $this->loadProductData($viewName);
            $this->loadVariantData($viewName);
            $this->loadLocationDescription($viewName);
        }
    }

    /**
     * Run the autocomplete action.
     * Returns a JSON string for the searched values.
     *
     * @return array
     */
    protected function autocompleteAction(): array
    {
        $source = $this->request->get('source', '');
        return match ($source) {
            'locations' => $this->autocompleteForLocations(),
            default => parent::autocompleteAction(),
        };
    }

    /**
     * Get autocomplete locations items for search user terms
     *
     * @return array
     */
    protected function autocompleteForLocations(): array
    {
        $data = $this->requestGet(['field', 'fieldcode', 'source', 'term', 'codewarehouse']);
        $where = $this->getAutocompleteWhere($data);
        $order = [ 'aisle' => 'ASC', 'rack' => 'ASC', 'shelf' => 'ASC', 'drawer' => 'ASC' ];

        $results = [];
        $location = new Location();
        foreach ($location->all($where, $order) as $row) {
            $results[] = ['key' => $row->id, 'value' => $row->descriptionComplete()];
        }

        if (empty($results)) {
            $i18n = Tools::lang();
            $results[] = ['key' => null, 'value' => $i18n->trans('no-data')];
        }

        return $results;
    }

    /**
     * Return array of where filters from user form data
     *
     * @param array $data
     * @return DataBaseWhere[]
     */
    private function getAutocompleteWhere(array $data): array
    {
        $result = empty($data['codewarehouse'])
            ? [ new DataBaseWhere('codewarehouse', null, 'IS') ]
            : [ new DataBaseWhere('codewarehouse', $data['codewarehouse']) ];

        foreach ($this->getColumnValuesWhere($data['term']) as $condition) {
            $result[] = $condition;
        }
        return $result;
    }

    /**
     * Get correct database where filter for user terms in base filter columns
     *
     * @param string $values
     * @return DataBaseWhere[]
     */
    private function getColumnValuesWhere(string $values): array
    {
        $result = [];
        $column1 = explode('|', 'aisle|rack|shelf|drawer');
        $column2 = explode(' ', $values);
        $maxValues = count($column1) - 1;

        for ($index = 0; $index < count($column2); $index++) {
            $result[] = new DataBaseWhere($column1[$index], mb_strtolower($column2[$index], 'UTF8'), 'LIKE');
            if ($index == $maxValues) {
                return $result;
            }
        }

        return $result;
    }

    /**
     * Get an array list for Widget Select of all References of one product
     *
     * @param int $idproduct
     * @return array
     */
    private function getReferencesForProduct(int $idproduct): array
    {
        $where = [ new DataBaseWhere('idproducto', $idproduct) ];
        $order = [ 'referencia' => 'ASC' ];
        $result = [];

        $variant = new Variante();
        foreach ($variant->all($where, $order) as $row) {
            $description = $row->description(true);
            $title = empty($description)
                ? $row->referencia
                : $row->referencia . ' : ' . $description;

            $result[] = ['value' => $row->referencia, 'title' => $title];
        }
        return $result;
    }

    /**
     * Create variant product model and load data
     *
     * @param string $viewName
     */
    private function loadLocationDescription(string $viewName): void
    {
        $idlocation = $this->getViewModelValue($viewName, 'idlocation');
        if (empty($idlocation)) {
            return;
        }

        $location = new Location();
        if (false === $location->load($idlocation)) {
            return;
        }

        $this->views[$viewName]->model->storagetype = $location->storagetype;

        $columnLocation = $this->views[$viewName]->columnForName('location');
        $columnLocation?->widget->setSelected($location->descriptionComplete());
    }

    /**
     * Create product model and load data
     *
     * @param string $viewName
     */
    private function loadProductData(string $viewName): void
    {
        $idproduct = $this->getViewModelValue($viewName, 'idproduct');
        if (empty($idproduct)) {
            return;
        }

        $product = new Producto();
        if ($product->load($idproduct)) {
            // Inject the product values into the main model. Is necessary for the xml view.
            $mainModel = $this->getModel();
            $mainModel->productreference = $product->referencia;
            $mainModel->productdescription = $product->descripcion;
        }
    }

    /**
     * Create variant product model and load data
     *
     * @param string $viewName
     */
    private function loadVariantData(string $viewName): void
    {
        $idproduct = $this->getViewModelValue($viewName, 'idproduct');
        if (empty($idproduct)) {
            return;
        }

        // Add variant data to widget select array
        $columnReference = $this->views[$viewName]->columnForName('reference');
        if ($columnReference) {
            $values = $this->getReferencesForProduct($idproduct);
            $columnReference->widget->setValuesFromArray($values);
        }
    }
}

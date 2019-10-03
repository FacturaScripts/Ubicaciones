<?php
/**
 * This file is part of FacturaScripts
 * Copyright (C) 2017-2018 Carlos Garcia Gomez <carlos@facturascripts.com>
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

use FacturaScripts\Core\Controller\ListAlmacen as ParentController;
use FacturaScripts\Plugins\Ubicaciones\Model\Location;

/**
 *  Controller to list the items in the Location model
 *
 * @author Daniel fernández <hola@danielfg.es>
 * @author Artex Trading sa <jcuello@artextrading.com>
 */
class ListAlmacen extends ParentController
{
    /**
     * Load views
     */
    protected function createViews()
    {
        parent::createViews();
        $this->createViewLocations();
    }

    /**
     * 
     * @param string $viewName
     */
    protected function createViewLocations($viewName = 'ListLocation')
    {
        $this->addView($viewName, 'Location', 'locations', 'fas fa-map-marker-alt');
        $this->addSearchFields($viewName, ['aisle', 'rack', 'shelf', 'bin']);
        $this->addOrderBy($viewName, ['codewarehouse', 'aisle', 'rack', 'shelf', 'bin'], 'warehouse');
        $this->addOrderBy($viewName, ['aisle', 'rack', 'shelf', 'bin', 'codewarehouse'], 'aisle');
        
        $warehouseValues = $this->codeModel->all('almacenes', 'codalmacen', 'nombre');
        $this->addFilterSelect($viewName, 'warehouse', 'warehouse', 'codewarehouse', $warehouseValues);        

        $aisleValues = $this->codeModel->all('locations', 'aisle', 'aisle');
        $this->addFilterSelect($viewName, 'aisle', 'aisle', 'aisle', $aisleValues);        
        
        $this->addFilterSelect($viewName, 'storage-type', 'type', 'storage_type', Location::getFilterSelectValues());        
    }
}
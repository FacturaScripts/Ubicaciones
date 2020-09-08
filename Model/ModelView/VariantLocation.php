<?php
/**
 * This file is part of Ubicaciones plugin for FacturaScripts.
 * Copyright (C) 2019 Jose Antonio Cuello Principal <jcuello@artextrading.com>
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

namespace FacturaScripts\Plugins\Ubicaciones\Model\ModelView;

use FacturaScripts\Core\Model\Base\ModelView;
use FacturaScripts\Plugins\Ubicaciones\Model\VariantLocation as VariantLocationModel;

/**
 * Location of Variant products. Model View.
 *
 * @author Jose Antonio Cuello Principal <jcuello@artextrading.com>
 */
class VariantLocation extends ModelView
{

    /**
     * Constructor and class initializer.
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);

        $this->setMasterModel(new VariantLocationModel());
    }

    protected function getTables(): array
    {
        return [
            'variantslocations',
            'locations',
            'almacenes',
            'variantes'
        ];
    }

    protected function getFields(): array
    {
        return [
            'id' => 'variantslocations.id',
            'idlocation' => 'variantslocations.idlocation',
            'idproduct' => 'variantslocations.idproduct',
            'reference' => 'variantslocations.reference',
            'aisle' => 'locations.aisle',
            'drawer' => 'locations.drawer',
            'codewarehouse' => 'locations.codewarehouse',
            'rack' => 'locations.rack',
            'shelf' => 'locations.shelf',
            'storagetype' => 'locations.storagetype',
            'validationcode' => 'locations.validationcode',
            'namewarehouse' => 'almacenes.nombre',
            'nameproduct' => 'productos.descripcion',
            'reference' => 'variantes.referencia',
            'barcode' => 'variantes.codbarras',
            'idattribute1' => 'variantes.idatributovalor1',
            'idattribute2' => 'variantes.idatributovalor2',
            'nameattribute1' => 'attribute1.descripcion',
            'nameattribute2' => 'attribute2.descripcion'
        ];
    }

    protected function getSQLFrom(): string {
        return 'variantslocations'
            . ' INNER JOIN productos ON productos.idproducto = variantslocations.idproduct'
            . ' INNER JOIN variantes ON variantes.referencia = variantslocations.reference'
            . ' INNER JOIN locations ON locations.id = variantslocations.idlocation'
            . ' LEFT JOIN almacenes ON almacenes.codalmacen = locations.codewarehouse'
            . ' LEFT JOIN atributos_valores attribute1 ON attribute1.id = variantes.idatributovalor1'
            . ' LEFT JOIN atributos_valores attribute2 ON attribute2.id = variantes.idatributovalor2';
    }
}

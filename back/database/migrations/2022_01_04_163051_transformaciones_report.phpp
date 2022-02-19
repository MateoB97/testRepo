<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransformacionesReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW [dbo].[transformaciones] as
        select
        tp.id tfrm,
        (select pd.nombre
        from productos pd
        where pd.id = tp.producto_id_in) entra,
        (select pd.nombre
        from productos pd
        where pd.id = tp.producto_id_out) sale,
        tp.cantidad_out cant_out,
        tp.producto_id_in id_in,
        tp.producto_id_out id_out,
        (select pd.prod_subgrupo_id
        from productos pd
        inner join prod_subgrupos p_sg on p_sg.id = pd.prod_subgrupo_id
        where tp.producto_id_in = pd.id) sub_p_in,
        (select nombre
        from prod_subgrupos psg
        where (select pd.prod_subgrupo_id
        from productos pd
        inner join prod_subgrupos p_sg on p_sg.id = pd.prod_subgrupo_id
        where tp.producto_id_in = pd.id) = psg.id) sub_nom_in,
        (select p_sg.id
        from prod_subgrupos p_sg
        inner join productos pd on p_sg.id = pd.prod_subgrupo_id
        where tp.producto_id_out = pd.id) sub_p_out,
        (select nombre
        from prod_subgrupos psg
        where (select pd.prod_subgrupo_id
        from productos pd
        inner join prod_subgrupos p_sg on p_sg.id = pd.prod_subgrupo_id
        where tp.producto_id_out = pd.id) = psg.id) sub_nom_out,
        (select p_g.id
        from prod_grupos p_g
        inner join prod_subgrupos p_sg on p_sg.prodGrupo_id = p_g.id
        where (select pd.prod_subgrupo_id
        from productos pd
        inner join prod_subgrupos p_sg on p_sg.id = pd.prod_subgrupo_id
        where tp.producto_id_in = pd.id) = p_sg.id) gp_in,
        (select nombre
        from prod_grupos pg
        where (select p_g.id
        from prod_grupos p_g
        inner join prod_subgrupos p_sg on p_sg.prodGrupo_id = p_g.id
        where (select pd.prod_subgrupo_id
        from productos pd
        inner join prod_subgrupos p_sg on p_sg.id = pd.prod_subgrupo_id
        where tp.producto_id_in = pd.id) = p_sg.id) = pg.id ) gp_in_nom,
        (select p_g.id
        from prod_grupos p_g
        inner join prod_subgrupos p_sg on p_sg.prodGrupo_id = p_g.id
        where (select pd.prod_subgrupo_id
        from productos pd
        inner join prod_subgrupos p_sg on p_sg.id = pd.prod_subgrupo_id
        where tp.producto_id_out = pd.id) = p_sg.id) gp_out,
        (select nombre
        from prod_grupos pg
        where (select p_g.id
        from prod_grupos p_g
        inner join prod_subgrupos p_sg on p_sg.prodGrupo_id = p_g.id
        where (select pd.prod_subgrupo_id
        from productos pd
        inner join prod_subgrupos p_sg on p_sg.id = pd.prod_subgrupo_id
        where tp.producto_id_out = pd.id) = p_sg.id) = pg.id ) gp_out_nom,
        tp.created_at fecha
        from transformacion_producto tp"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW if exists transformaciones');
    }
}

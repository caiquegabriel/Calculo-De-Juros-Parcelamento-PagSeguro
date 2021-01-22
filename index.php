<?php

    require_once('Parcelamento.php'); 
    
    $Parcelamento = new Parcelamento();
    $Parcelamento -> set_installment_quantity( 3 );
    $Parcelamento -> set_installment_no_interest( 0);
    $Parcelamento -> set_checkout_value( 15000 );
    $parcelas      = $Parcelamento -> get_installments();
    
    if( is_null( $parcelas ) ){

        return;
    }

    foreach( $parcelas as $parcela ){
        echo '<p>'.$parcela.'</p>';
    }
?>
<?php 

    /*
        Classe auxilia no cálculo de taxas do parcelamento usando a API do PagSeguro.
    */ 

    class Parcelamento{

        /*
            Juros por parcela 
            ( Da parcela 1 => Até a parcela 16 )
            @Var (array)
        */
        protected $_fees                    = [2.99, 4.51, 6.04, 7.59, 9.15, 10.72, 12.31, 13.92, 15.54, 17.17, 18.82, 20.48, 22.16, 26.85, 25.56, 27.28];

        /*
            Informações de cada parcela
            @Var (array)
        */
        protected $_installments            = [];

        /*
            Array com erros
            @Var (array)
        */
        protected $_errors                  = [];

        /*
            Quantidade de parcelas que foi solicitado no pagamento
            @Var (int)
        */
        protected $_installments_quantity   = 0;

        /*
            Limite de quantidade de parcelas 
            @Var (int)
        */
        protected $_installments_quantity_max = 16;

        /*
            Quantidade de parcelas sem juros
            @Var (int)
        */
        protected $_installment_no_interest = 0;

        /*
            Valor do pagamento
            @Var (float)
        */
        protected $_checkout_value          = 0;


        /*
            Quantidade de parcelas 
            @Params (int) $installments_quantity
        */
        public function set_installment_quantity( $installments_quantity ){
            $this -> _installments_quantity = $installments_quantity;
        }

        /*
            Quantidade de parcelas sem juros
            @Params (int) $installment_no_interest
        */
        public function set_installment_no_interest($installment_no_interest){
            $this -> _installment_no_interest = $installment_no_interest;
        }

        /*
            Valor do checkout
            @Params (float) $checkout_value
        */
        public function set_checkout_value($checkout_value){
            $this -> _checkout_value = $checkout_value;
        }

        /*
            Seta a informação de uma parcela
            @Params (string) $installment 
        */
        protected function _set_installment( $installment ){
            $this -> _installments[] = $installment;
        }

        /*
            Seta um erro.
            @Params (string) $error;
        */
        protected function _set_error( $error ){
            if(is_string($error)){
                $this -> _errors[] = $error;
            }
        }

        /*
            @Returns (array) : Erros;
        */
        public function get_errors(){
            return $this -> _errors;
        }

        /*
            @Returns (bool) : Tem erro;
        */
        public function has_error(){
            if( !empty($this -> _errors ) )
                return true;
            return false;
        }

        public function get_installments(){

            $installments_quantity   = $this -> _installments_quantity;
            $installment_no_interest = $this -> _installment_no_interest;
            $checkout_value          = $this -> _checkout_value; 


            if( $checkout_value <= 0 || $installments_quantity <= 0 ){
                $this -> _set_error('Checkout deve ter um valor superior a 0!');
            }  

            if( $installments_quantity <= 0 || $installments_quantity > 16 ){
                $this -> _set_error('O parcelamento só pode ser feito entre 1 e ' . $this -> _installments_quantity_max .' vezes !' );
            }

            if( $this -> has_error() )
                return;

            if( $checkout_value <= 5.00 ){
                $this->_set_installment('1x de R$'.($payment_value_installment).' ( á vista )');
                
                return $this -> _installments;
            } 

            for( $i = 1; $i <= $installments_quantity; $i++ ){ 

                if( $checkout_value / $i < 5.00 ){
                    break;
                }

                if( ($checkout_value /($i+1)) < 5.00  ){ 
                    $last_installment = true;
                } 

                if( $i <= $installment_no_interest || $i < 3 ){
                    /*
                        SEM JUROS 
                    */
                    
                    //Valor a ser pago
                    $payment_value = $checkout_value ; 

                }else{ 
                    /*
                        COM JUROS 
                    */

                    //Taxa de juros deve ser igual a (parcela corrente - 1 )
                    $rate                            = $this->_fees[$i-1];
 
                    //Valor a ser pago
                    $payment_value = ( $checkout_value + ( $checkout_value * $rate)  / 100 ) ;//parcela  
                }

                $payment_value_installment = round($payment_value/$i, 2);
                $payment_value_installment = number_format( $payment_value_installment , 2, '.', ','); 
                $payment_value             = number_format( $payment_value , 2, '.', ',');

                if( isset($last_installment) && $i === 1 || $i === 1){
                    $this->_set_installment($i.'x de R$'.($payment_value_installment).' ( á vista )');
                }else{
                    $this->_set_installment($i.'x de R$'.($payment_value_installment).' ( R$ '.$payment_value .')');
                }
                 
            }   


            return $this -> _installments;
        }
    }


?>

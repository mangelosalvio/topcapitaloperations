new Vue({
    el : "#app",

    ready : function(){

        var e = this;
        this.$http.get('/api/chart-of-accounts').then(function(response){
            this.chart_of_accounts = response.json();
        });

        if ( this.general_ledger_id != null ) {
            this.$http.get('/api/general-ledgers/' + this.general_ledger_id + "/general-ledger-details").then(function(response){
                var details = response.json();

                $.each(details, function(i, detail){
                    e.general_ledger_details.push({
                        chart_of_account_id : detail.id,
                        debit : detail.pivot.debit,
                        credit : detail.pivot.credit,
                        description : detail.pivot.description
                    });
                });
            });
        }
    },

    computed : {
        total_debit : function () {
            var total = numeral(0) ;

            $.each(this.general_ledger_details, function(i, general_ledger_detail){
                total.add(general_ledger_detail.debit);
            });

            return numeral(total.value()).format('0,0.00');
        },

        total_credit : function () {
            var total = numeral(0) ;

            $.each(this.general_ledger_details, function(i, general_ledger_detail){
                total.add(general_ledger_detail.credit);
            });

            return numeral(total.value()).format('0,0.00');
        }


    },

    data : {
        detail : {  debit : null, credit : null, chart_of_account_id : null, description : null },
        chart_of_accounts : null,
        general_ledger_details : [],
        general_ledger_id : null
    },

    methods : {
        addDetail : function() {
            this.general_ledger_details.push(Vue.util.extend({},this.detail));
            this.detail.debit = "";
            this.detail.credit = "";
            this.detail.description = "";
        },
        removeDetail : function(general_ledger_detail) {
            console.log(JSON.stringify(general_ledger_detail));
            var e = this;
            this.$http.delete('/api/general-ledgers/' + e.general_ledger_id+ "/account/" + general_ledger_detail.chart_of_account_id + "/delete").then(function(response){
                console.log(response);
                e.general_ledger_details.$remove(general_ledger_detail);
            });

        }
    }
});
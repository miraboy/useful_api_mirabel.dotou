import { defineStore } from "pinia";
import { api } from "@/services/myApi";

export const useWalletStore = defineStore("wallet", {
    state: () => ({
        wallet: null,
        transactions: [],
        loading: false,
        loadingTransactions: false,
        loadingTopup: false,
        loadingTransfer: false,
        error: null,
        successMessage: null,
    }),

    getters: {
        balance: (state) => state.wallet?.balance || 0,
        formattedBalance: (state) => {
            const balance = state.wallet?.balance || 0;
            return new Intl.NumberFormat("fr-FR", {
                style: "currency",
                currency: "XOF",
            }).format(balance);
        },
    },

    actions: {
        // Récupérer le wallet de l'utilisateur
        async fetchWallet() {
            this.loading = true;
            this.error = null;

            const res = await api.get("/wallet");
            this.loading = false;

            if (res.success) {
                this.wallet = res.data;
            } else {
                this.error =
                    res.message || "Erreur lors de la récupération du wallet";
            }
        },

        // Récupérer l'historique des transactions
        async fetchTransactions() {
            this.loadingTransactions = true;
            this.error = null;

            const res = await api.get("/wallet/transactions");
            this.loadingTransactions = false;

            if (res.success) {
                this.transactions = res.data;
            } else {
                this.error =
                    res.message ||
                    "Erreur lors de la récupération des transactions";
            }
        },

        // Top-up : ajouter de l'argent
        async topup(amount) {
            this.loadingTopup = true;
            this.error = null;
            this.successMessage = null;

            const res = await api.post("/wallet/topup", { amount });
            this.loadingTopup = false;

            if (res.success) {
                this.successMessage = "Recharge effectuée avec succès !";
                // Rafraîchir le wallet pour avoir le solde à jour
                await this.fetchWallet();
                // Rafraîchir les transactions
                await this.fetchTransactions();
                return true;
            } else {
                this.error =
                    res.message ||
                    res.data?.message ||
                    "Erreur lors de la recharge";
                return false;
            }
        },

        // Transfer : envoyer de l'argent à un autre utilisateur
        async transfer(data) {
            this.loadingTransfer = true;
            this.error = null;
            this.successMessage = null;

            const res = await api.post("/wallet/transfer", data);
            this.loadingTransfer = false;

            if (res.success) {
                this.successMessage = `Transfert de ${data.amount} effectué avec succès !`;
                await this.fetchWallet();

                await this.fetchTransactions();
                return true;
            } else {
                this.error =
                    res.message ||
                    res.data?.message ||
                    "Erreur lors du transfert";
                return false;
            }
        },

        // Clear messages
        clearMessages() {
            this.error = null;
            this.successMessage = null;
        },
    },
});

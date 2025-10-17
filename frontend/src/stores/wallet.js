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
        // Fetch user wallet
        async fetchWallet() {
            this.loading = true;
            this.error = null;

            const res = await api.get("/wallet");
            this.loading = false;

            if (res.success) {
                this.wallet = res.data;
            } else {
                this.error = res.message || "Error fetching wallet";
            }
        },

        // Fetch transaction history
        async fetchTransactions() {
            this.loadingTransactions = true;
            this.error = null;

            const res = await api.get("/wallet/transactions");
            this.loadingTransactions = false;

            if (res.success) {
                this.transactions = res.data;
            } else {
                this.error = res.message || "Error fetching transactions";
            }
        },

        // Top-up: add money
        async topup(amount) {
            this.loadingTopup = true;
            this.error = null;
            this.successMessage = null;

            const res = await api.post("/wallet/topup", { amount });
            this.loadingTopup = false;

            if (res.success) {
                this.successMessage = "Top-up completed successfully!";
                // Refresh wallet to get updated balance
                await this.fetchWallet();
                // Refresh transactions
                await this.fetchTransactions();
                return true;
            } else {
                this.error =
                    res.message || res.data?.message || "Error during top-up";
                return false;
            }
        },

        // Transfer: send money to another user
        async transfer(data) {
            this.loadingTransfer = true;
            this.error = null;
            this.successMessage = null;

            const res = await api.post("/wallet/transfer", data);
            this.loadingTransfer = false;

            if (res.success) {
                this.successMessage = `Transfer of ${data.amount} completed successfully!`;
                await this.fetchWallet();

                await this.fetchTransactions();
                return true;
            } else {
                this.error =
                    res.message || res.data?.message || "Error during transfer";
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

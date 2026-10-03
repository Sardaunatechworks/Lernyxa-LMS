import { create } from "zustand";
import { User } from "@/types";
import { apiClient, getCsrfCookie } from "@/lib/api";

interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
  setUser: (user: User | null) => void;
  setLoading: (isLoading: boolean) => void;
  setError: (error: string | null) => void;
  login: (credentials: { email: string; password: string; remember?: boolean }) => Promise<User>;
  register: (data: {
    first_name: string;
    last_name: string;
    email: string;
    password: string;
    password_confirmation: string;
    phone?: string;
    tenant_slug?: string;
  }) => Promise<User>;
  fetchCurrentUser: () => Promise<User | null>;
  logout: () => Promise<void>;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  isAuthenticated: false,
  isLoading: true,
  error: null,

  setUser: (user) =>
    set({
      user,
      isAuthenticated: !!user,
      isLoading: false,
      error: null,
    }),

  setLoading: (isLoading) => set({ isLoading }),
  setError: (error) => set({ error }),

  login: async (credentials) => {
    set({ isLoading: true, error: null });
    try {
      // 1. Fetch CSRF cookie from Laravel Sanctum
      try {
        await getCsrfCookie();
      } catch {
        // Fallback for direct token / testing API
      }

      // 2. Submit credentials
      const response = await apiClient.post("/auth/login", credentials);
      const user = response.data.user as User;

      if (response.data.token && typeof window !== "undefined") {
        localStorage.setItem("lernyxa_token", response.data.token);
      }

      set({
        user,
        isAuthenticated: true,
        isLoading: false,
        error: null,
      });

      return user;
    } catch (err: unknown) {
      let message = "Invalid email or password";
      if (err && typeof err === "object" && "response" in err) {
        const axiosErr = err as { response?: { data?: { message?: string } } };
        message = axiosErr.response?.data?.message || message;
      }
      set({ isLoading: false, error: message });
      throw new Error(message);
    }
  },

  register: async (data) => {
    set({ isLoading: true, error: null });
    try {
      try {
        await getCsrfCookie();
      } catch {
        // Fallback
      }

      const response = await apiClient.post("/auth/register", data);
      const user = response.data.user as User;

      if (response.data.token && typeof window !== "undefined") {
        localStorage.setItem("lernyxa_token", response.data.token);
      }

      set({
        user,
        isAuthenticated: true,
        isLoading: false,
        error: null,
      });

      return user;
    } catch (err: unknown) {
      let message = "Registration failed";
      if (err && typeof err === "object" && "response" in err) {
        const axiosErr = err as { response?: { data?: { message?: string } } };
        message = axiosErr.response?.data?.message || message;
      }
      set({ isLoading: false, error: message });
      throw new Error(message);
    }
  },

  fetchCurrentUser: async () => {
    set({ isLoading: true });
    try {
      const response = await apiClient.get("/auth/me");
      const user = response.data.user as User;
      set({
        user,
        isAuthenticated: true,
        isLoading: false,
        error: null,
      });
      return user;
    } catch {
      set({
        user: null,
        isAuthenticated: false,
        isLoading: false,
      });
      return null;
    }
  },

  logout: async () => {
    try {
      await apiClient.post("/auth/logout");
    } catch {
      // Local cleanup even if API fails
    } finally {
      if (typeof window !== "undefined") {
        localStorage.removeItem("lernyxa_token");
      }
      set({
        user: null,
        isAuthenticated: false,
        isLoading: false,
        error: null,
      });
    }
  },
}));

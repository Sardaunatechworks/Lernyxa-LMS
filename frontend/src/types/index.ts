export type Role =
  | "super_admin"
  | "tenant_admin"
  | "instructor"
  | "teaching_assistant"
  | "learner";

export interface User {
  id: string;
  name: string;
  email: string;
  avatar_url?: string | null;
  phone?: string | null;
  roles: Role[];
  permissions?: string[];
  tenant_id?: string | null;
  email_verified_at?: string | null;
  created_at: string;
  updated_at: string;
}

export interface Tenant {
  id: string;
  name: string;
  slug: string;
  logo_url?: string | null;
  domain?: string | null;
  is_active: boolean;
  settings?: Record<string, unknown>;
  created_at: string;
  updated_at: string;
}

export interface Cohort {
  id: string;
  tenant_id: string;
  name: string;
  slug: string;
  description?: string | null;
  start_date: string;
  end_date: string;
  capacity: number;
  enrolled_count: number;
  status: "upcoming" | "active" | "completed" | "archived";
  created_at: string;
  updated_at: string;
}

export interface Course {
  id: string;
  tenant_id: string;
  cohort_id?: string | null;
  title: string;
  slug: string;
  description: string;
  thumbnail_url?: string | null;
  instructor_id: string;
  status: "draft" | "published" | "archived";
  modules_count?: number;
  created_at: string;
  updated_at: string;
}

export interface ApiResponse<T = unknown> {
  success: boolean;
  message?: string;
  data: T;
  meta?: {
    current_page?: number;
    last_page?: number;
    per_page?: number;
    total?: number;
  };
}

"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import {
  GraduationCap,
  Users,
  ShieldCheck,
  Video,
  Award,
  BookOpen,
  CalendarCheck,
  BarChart3,
  Bell,
  Sparkles,
  Server,
  Database,
  ArrowRight,
  CheckCircle2,
  Cpu,
  Layers,
  Globe,
  Terminal,
  Activity,
  ChevronRight,
  ExternalLink,
} from "lucide-react";

export default function Home() {
  const [apiStatus, setApiStatus] = useState<"checking" | "online" | "offline">("checking");
  const [apiLatency, setApiLatency] = useState<number | null>(null);

  useEffect(() => {
    const checkApi = async () => {
      const startTime = performance.now();
      try {
        const res = await fetch("http://localhost:8000/api/v1/health", {
          method: "GET",
          headers: { Accept: "application/json" },
        });
        if (res.ok) {
          const endTime = performance.now();
          setApiLatency(Math.round(endTime - startTime));
          setApiStatus("online");
        } else {
          setApiStatus("offline");
        }
      } catch {
        setApiStatus("offline");
      }
    };
    checkApi();
  }, []);

  const roles = [
    {
      title: "Super Admin",
      badge: "Platform Level",
      desc: "Full visibility across all tenants, global analytics, system configuration, audit logs, and infrastructure controls.",
      icon: ShieldCheck,
      color: "from-amber-500/20 to-orange-500/20 border-amber-500/30 text-amber-400",
    },
    {
      title: "Tenant Admin",
      badge: "Organization Level",
      desc: "Manages cohorts, assigns instructors, monitors organization-level enrollment, billing, and custom branding.",
      icon: Users,
      color: "from-blue-500/20 to-indigo-500/20 border-blue-500/30 text-blue-400",
    },
    {
      title: "Instructor",
      badge: "Curriculum & Delivery",
      desc: "Schedules Zoom live sessions, curates learning modules, grades project submissions, and hosts mentor office hours.",
      icon: GraduationCap,
      color: "from-violet-500/20 to-purple-500/20 border-violet-500/30 text-violet-400",
    },
    {
      title: "Teaching Assistant",
      badge: "Academic Support",
      desc: "Facilitates discussion forums, tracks attendance, answers student queries, and reviews preliminary homework.",
      icon: BookOpen,
      color: "from-emerald-500/20 to-teal-500/20 border-emerald-500/30 text-emerald-400",
    },
    {
      title: "Learner",
      badge: "Cohort Student",
      desc: "Participates in live cohorts, completes assignments, submits project milestones, and earns verifiable PDF credentials.",
      icon: Award,
      color: "from-cyan-500/20 to-sky-500/20 border-cyan-500/30 text-cyan-400",
    },
  ];

  const modules = [
    { icon: ShieldCheck, name: "Auth & RBAC", desc: "Laravel Sanctum SPA auth & 5-tier Spatie role matrix" },
    { icon: Globe, name: "Multi-Tenancy", desc: "Isolated organizational environments & custom subdomain support" },
    { icon: Users, name: "Cohort Management", desc: "500-student cohort scaling, start/end dates, and pacing gates" },
    { icon: BookOpen, name: "Course Curriculum", desc: "Modular chapters, video embeds, quizzes, and resource attachments" },
    { icon: Video, name: "Zoom Live Sessions", desc: "Meeting SDK embedded delivery, recordings & automated attendance" },
    { icon: CalendarCheck, name: "Attendance Tracking", desc: "Real-time session join logs, duration checks & participation scores" },
    { icon: Terminal, name: "Code Submissions", desc: "Milestone code repos, peer reviews & instructor rubrics" },
    { icon: Award, name: "Verifiable Certificates", desc: "Spatie PDF generation with dynamic cryptographically signed QR codes" },
    { icon: Bell, name: "Resend Notifications", desc: "Automated cohort reminders, grade releases & direct alerts" },
    { icon: BarChart3, name: "Cohort Analytics", desc: "Dropout prediction, completion velocity & engagement telemetry" },
    { icon: Database, name: "Supabase & Postgres", desc: "Robust relational data layer with full-text search and storage" },
    { icon: Server, name: "Redis & Queue Workers", desc: "Asynchronous background processing for emails and video jobs" },
  ];

  const techStack = [
    { label: "Frontend", val: "Next.js 16 (App Router) + TypeScript", color: "bg-blue-500/10 text-blue-400 border-blue-500/20" },
    { label: "Backend API", val: "Laravel 13 + PHP 8.3", color: "bg-red-500/10 text-red-400 border-red-500/20" },
    { label: "Authentication", val: "Laravel Sanctum (Cookie SPA)", color: "bg-indigo-500/10 text-indigo-400 border-indigo-500/20" },
    { label: "Database", val: "PostgreSQL / Supabase", color: "bg-emerald-500/10 text-emerald-400 border-emerald-500/20" },
    { label: "Cache & Queue", val: "Redis 7 Alpine", color: "bg-rose-500/10 text-rose-400 border-rose-500/20" },
    { label: "Live Meetings", val: "Zoom Meeting SDK & REST API", color: "bg-cyan-500/10 text-cyan-400 border-cyan-500/20" },
    { label: "PDF Engine", val: "Spatie Laravel PDF (Browsershot)", color: "bg-purple-500/10 text-purple-400 border-purple-500/20" },
    { label: "Email Service", val: "Resend API", color: "bg-amber-500/10 text-amber-400 border-amber-500/20" },
  ];

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 selection:bg-indigo-500 selection:text-white">
      {/* Top Navigation */}
      <header className="sticky top-0 z-50 backdrop-blur-xl bg-slate-950/80 border-b border-slate-800/80">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="h-10 w-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/25 ring-1 ring-white/20">
              <GraduationCap className="h-6 w-6 text-white" />
            </div>
            <div>
              <span className="font-bold text-xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-200 to-slate-400">
                Lernyxa
              </span>
              <span className="ml-1.5 text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                LMS
              </span>
            </div>
          </div>

          <nav className="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
            <a href="#features" className="hover:text-white transition-colors">Features</a>
            <a href="#roles" className="hover:text-white transition-colors">Roles</a>
            <a href="#architecture" className="hover:text-white transition-colors">Architecture</a>
            <a href="#status" className="hover:text-white transition-colors">System Status</a>
          </nav>

          <div className="flex items-center gap-3">
            <a
              href="https://github.com/Sardaunatechworks/Lernyxa-LMS"
              target="_blank"
              rel="noreferrer"
              className="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-slate-300 hover:text-white bg-slate-900 border border-slate-800 hover:border-slate-700 transition-colors"
            >
              <Cpu className="h-3.5 w-3.5" />
              GitHub
              <ExternalLink className="h-3 w-3" />
            </a>
            <button className="px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition-all hover:scale-[1.02]">
              Launch Console
            </button>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative overflow-hidden pt-20 pb-28">
        <div className="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_-20%,rgba(99,102,241,0.25),rgba(255,255,255,0))]" />
        
        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-950/60 border border-indigo-500/30 text-indigo-300 text-xs font-medium mb-8 backdrop-blur-sm animate-pulse">
            <Sparkles className="h-3.5 w-3.5 text-indigo-400" />
            <span>Built by Sardauna Tech Lab Ltd • Enterprise Cohort-Aware Architecture</span>
          </div>

          <h1 className="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white max-w-5xl mx-auto leading-[1.1]">
            Learn. Build. Progress. <br />
            <span className="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-violet-300 to-cyan-300">
              The Modern Cohort LMS.
            </span>
          </h1>

          <p className="mt-6 text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
            Engineered specifically for high-engagement, cohort-based tech training. Seamlessly coordinates 
            live Zoom sessions, automated attendance, progressive course gates, and cryptographically verifiable certificates.
          </p>

          <div className="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a
              href="#architecture"
              className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-violet-600 hover:from-indigo-500 hover:to-violet-500 shadow-xl shadow-indigo-600/25 transition-all hover:scale-[1.02]"
            >
              Explore Architecture
              <ArrowRight className="h-4 w-4" />
            </a>
            <a
              href="#status"
              className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-slate-200 bg-slate-900/90 border border-slate-800 hover:border-slate-700 hover:bg-slate-800/90 transition-all"
            >
              <Activity className="h-4 w-4 text-emerald-400" />
              Live Health Status
            </a>
          </div>

          {/* Quick Metrics */}
          <div className="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
            {[
              { num: "500", label: "Learners / Cohort" },
              { num: "5", label: "Enterprise Roles" },
              { num: "12", label: "Specialized Modules" },
              { num: "100%", label: "Verifiable Certs" },
            ].map((stat, i) => (
              <div key={i} className="p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 backdrop-blur-sm">
                <div className="text-2xl sm:text-3xl font-extrabold text-white">{stat.num}</div>
                <div className="text-xs text-slate-400 mt-1 uppercase tracking-wider font-medium">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Tech Stack Matrix */}
      <section id="architecture" className="py-20 border-t border-slate-800/80 bg-slate-900/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-3xl mx-auto mb-14">
            <h2 className="text-xs font-bold uppercase tracking-widest text-indigo-400 mb-2">Technical Foundations</h2>
            <p className="text-3xl font-bold text-white tracking-tight">Decoupled Full-Stack Architecture</p>
            <p className="mt-3 text-slate-400 text-sm">
              Strict separation of concerns between our reactive Next.js client and resilient Laravel 13 API service.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {techStack.map((tech, idx) => (
              <div
                key={idx}
                className="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-slate-700 transition-all flex flex-col justify-between"
              >
                <div>
                  <span className={`inline-block px-2.5 py-1 text-xs font-semibold rounded-md border ${tech.color} mb-3`}>
                    {tech.label}
                  </span>
                  <div className="font-semibold text-slate-100 text-sm">{tech.val}</div>
                </div>
                <div className="mt-4 flex items-center text-xs text-emerald-400 gap-1.5 font-medium">
                  <CheckCircle2 className="h-3.5 w-3.5" />
                  <span>Configured & Spec-Aligned</span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 5 Enterprise Roles */}
      <section id="roles" className="py-20 border-t border-slate-800/80">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-3xl mx-auto mb-14">
            <h2 className="text-xs font-bold uppercase tracking-widest text-indigo-400 mb-2">Access & Permissions</h2>
            <p className="text-3xl font-bold text-white tracking-tight">Role-Based Experience Matrix</p>
            <p className="mt-3 text-slate-400 text-sm">
              Tailored workflows and interfaces engineered specifically for each cohort participant level.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {roles.map((r, i) => {
              const Icon = r.icon;
              return (
                <div
                  key={i}
                  className="p-5 rounded-2xl bg-slate-900/50 border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col justify-between"
                >
                  <div>
                    <div className={`h-11 w-11 rounded-xl bg-gradient-to-br ${r.color} flex items-center justify-center border mb-4`}>
                      <Icon className="h-5 w-5" />
                    </div>
                    <div className="font-bold text-white text-base">{r.title}</div>
                    <div className="text-[11px] font-semibold text-indigo-400 mb-3">{r.badge}</div>
                    <p className="text-xs text-slate-400 leading-relaxed">{r.desc}</p>
                  </div>
                  <div className="mt-5 pt-3 border-t border-slate-800/60 flex items-center text-xs text-slate-500 font-medium">
                    <span>RBAC Gated</span>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* 12 Core LMS Modules */}
      <section id="features" className="py-20 border-t border-slate-800/80 bg-slate-900/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-3xl mx-auto mb-14">
            <h2 className="text-xs font-bold uppercase tracking-widest text-indigo-400 mb-2">Capabilities</h2>
            <p className="text-3xl font-bold text-white tracking-tight">12 Core System Modules</p>
            <p className="mt-3 text-slate-400 text-sm">
              From authentication to certificate generation, every module is designed for scale and enterprise reliability.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {modules.map((m, idx) => {
              const Icon = m.icon;
              return (
                <div
                  key={idx}
                  className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-indigo-500/40 hover:bg-slate-900/90 transition-all group"
                >
                  <div className="flex items-start gap-4">
                    <div className="h-10 w-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                      <Icon className="h-5 w-5" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-slate-100 text-sm group-hover:text-indigo-300 transition-colors">
                        {m.name}
                      </h3>
                      <p className="text-xs text-slate-400 mt-1.5 leading-relaxed">{m.desc}</p>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Live System Diagnostics / Status */}
      <section id="status" className="py-20 border-t border-slate-800/80">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="p-8 rounded-3xl bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-800 shadow-2xl relative overflow-hidden">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
              <div className="flex items-center gap-3">
                <div className="h-3 w-3 rounded-full bg-emerald-500 animate-ping" />
                <span className="font-bold text-lg text-white">System Environment Status</span>
              </div>
              <div className="text-xs font-mono text-slate-400">
                Phase 1 Scaffolding: <span className="text-emerald-400 font-semibold">Ready</span>
              </div>
            </div>

            <div className="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div className="p-4 rounded-xl bg-slate-950/70 border border-slate-800">
                <div className="text-xs text-slate-400 font-medium">Frontend Layer</div>
                <div className="text-sm font-semibold text-white mt-1">Next.js 16 (App Router)</div>
                <div className="text-xs text-emerald-400 mt-2 flex items-center gap-1 font-mono">
                  <CheckCircle2 className="h-3 w-3" /> Online (Port 3000)
                </div>
              </div>

              <div className="p-4 rounded-xl bg-slate-950/70 border border-slate-800">
                <div className="text-xs text-slate-400 font-medium">API Service Layer</div>
                <div className="text-sm font-semibold text-white mt-1">Laravel 13 REST API</div>
                <div className="text-xs mt-2 flex items-center gap-1 font-mono">
                  {apiStatus === "online" ? (
                    <span className="text-emerald-400 flex items-center gap-1">
                      <CheckCircle2 className="h-3 w-3" /> Online ({apiLatency}ms)
                    </span>
                  ) : apiStatus === "checking" ? (
                    <span className="text-amber-400">Checking heartbeat...</span>
                  ) : (
                    <span className="text-slate-400">Dev daemon offline (Port 8000)</span>
                  )}
                </div>
              </div>

              <div className="p-4 rounded-xl bg-slate-950/70 border border-slate-800">
                <div className="text-xs text-slate-400 font-medium">Containerization</div>
                <div className="text-sm font-semibold text-white mt-1">Docker Compose Stack</div>
                <div className="text-xs text-indigo-400 mt-2 flex items-center gap-1 font-mono">
                  <Layers className="h-3 w-3" /> 7 Services Defined
                </div>
              </div>
            </div>

            <div className="mt-6 p-4 rounded-xl bg-slate-950/90 border border-slate-800 font-mono text-xs text-slate-300">
              <div className="flex items-center justify-between text-slate-500 mb-2">
                <span>Quick Diagnostic Endpoint</span>
                <span>GET /api/v1/health</span>
              </div>
              <div className="text-emerald-400">
                {`{ "status": "healthy", "platform": "Lernyxa LMS API", "version": "1.0.0" }`}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-12 border-t border-slate-900 bg-slate-950">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6">
          <div className="flex items-center gap-3">
            <div className="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
              <GraduationCap className="h-4 w-4 text-white" />
            </div>
            <span className="font-semibold text-white text-sm">Lernyxa LMS</span>
            <span className="text-xs text-slate-500">•</span>
            <span className="text-xs text-slate-400">Learn. Build. Progress.</span>
          </div>

          <div className="text-xs text-slate-500 text-center sm:text-right">
            Designed & Developed by{" "}
            <a
              href="https://sardaunatechlab.com"
              target="_blank"
              rel="noreferrer"
              className="text-indigo-400 hover:text-indigo-300 font-medium transition-colors"
            >
              Sardauna Tech Lab Ltd
            </a>
            . All rights reserved.
          </div>
        </div>
      </footer>
    </div>
  );
}

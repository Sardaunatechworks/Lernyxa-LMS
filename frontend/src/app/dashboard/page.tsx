"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/store/authStore";
import {
  GraduationCap,
  Users,
  Video,
  BookOpen,
  CalendarCheck,
  CheckCircle2,
  Clock,
  ArrowRight,
  LogOut,
  Bell,
  Sparkles,
  PlayCircle,
  Award,
  ChevronRight,
  Shield,
  Loader2,
} from "lucide-react";

export default function DashboardPage() {
  const router = useRouter();
  const { user, isAuthenticated, isLoading, logout, fetchCurrentUser } = useAuthStore();
  const [activeTab, setActiveTab] = useState<"overview" | "curriculum" | "sessions">("overview");

  useEffect(() => {
    // If not authenticated, check session
    if (!user) {
      fetchCurrentUser().then((u) => {
        if (!u) {
          router.push("/login");
        }
      });
    }
  }, [user, fetchCurrentUser, router]);

  const handleLogout = async () => {
    await logout();
    router.push("/login");
  };

  if (isLoading && !user) {
    return (
      <div className="min-h-screen bg-slate-950 flex items-center justify-center text-slate-300">
        <Loader2 className="h-8 w-8 animate-spin text-indigo-500" />
      </div>
    );
  }

  const primaryRole = user?.roles?.[0] || "learner";

  const cohortData = {
    name: "Full-Stack Web Engineering — Cohort 1",
    slug: "fsw-cohort-1",
    tenant: "Sardauna Tech Lab",
    capacity: 500,
    enrolledCount: 1,
    availableSlots: 499,
    progressPercentage: 33,
    status: "active",
    instructor: "Dr. Aminu Kano",
    nextSession: {
      title: "Zoom Live: Module 1 Architecture & Docker Deep Dive",
      time: "Tomorrow at 6:00 PM (WAT)",
      duration: "90 mins",
      zoomMeetingId: "98765432101",
    },
    modules: [
      {
        id: 1,
        title: "Module 1: Architecture & Monorepo Scaffolding",
        completed: 2,
        total: 2,
        lessons: [
          { id: 1, title: "Decoupled LMS Architecture Deep Dive", duration: "45m", completed: true },
          { id: 2, title: "Docker Compose 7-Service Walkthrough", duration: "35m", completed: true },
        ],
      },
      {
        id: 2,
        title: "Module 2: Live Zoom SDK & Automated Attendance",
        completed: 0,
        total: 2,
        lessons: [
          { id: 3, title: "Zoom Meeting SDK Embedding & Security Tokens", duration: "50m", completed: false },
          { id: 4, title: "Webhooks & Real-time Duration Attendance Logging", duration: "40m", completed: false },
        ],
      },
      {
        id: 3,
        title: "Module 3: Capstone Submissions & Cryptographic Certificates",
        completed: 0,
        total: 2,
        lessons: [
          { id: 5, title: "GitHub PR Code Submission & Grading Rubrics", duration: "45m", completed: false },
          { id: 6, title: "Spatie PDF Generation & Verifiable QR Signatures", duration: "60m", completed: false },
        ],
      },
    ],
  };

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 selection:bg-indigo-500 selection:text-white">
      {/* Top App Bar */}
      <header className="sticky top-0 z-40 backdrop-blur-xl bg-slate-950/80 border-b border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <Link href="/" className="flex items-center gap-2.5">
              <div className="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-400 flex items-center justify-center shadow-md shadow-indigo-500/20">
                <GraduationCap className="h-5 w-5 text-white" />
              </div>
              <div>
                <span className="font-bold text-lg text-white">Lernyxa</span>
                <span className="ml-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                  LMS
                </span>
              </div>
            </Link>

            <span className="hidden sm:inline-block text-slate-700">|</span>
            <div className="hidden sm:flex items-center gap-2 text-xs text-slate-400">
              <span className="h-2 w-2 rounded-full bg-emerald-500" />
              <span>{cohortData.tenant}</span>
            </div>
          </div>

          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2 text-right">
              <div>
                <div className="text-xs font-semibold text-white">{user?.name || "Student"}</div>
                <div className="text-[10px] font-mono text-indigo-400 uppercase">{primaryRole.replace("_", " ")}</div>
              </div>
              <div className="h-8 w-8 rounded-full bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 font-bold flex items-center justify-center text-xs">
                {(user?.name || "U")[0]}
              </div>
            </div>

            <button
              onClick={handleLogout}
              className="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-colors"
              title="Sign Out"
            >
              <LogOut className="h-4 w-4" />
            </button>
          </div>
        </div>
      </header>

      {/* Main Content Area */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Welcome Header */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-8">
          <div>
            <h1 className="text-2xl sm:text-3xl font-bold text-white tracking-tight">
              Welcome back, {user?.first_name || user?.name || "Learner"}!
            </h1>
            <p className="text-xs sm:text-sm text-slate-400 mt-1">
              Active Cohort: <span className="text-indigo-400 font-medium">{cohortData.name}</span>
            </p>
          </div>

          <div className="flex items-center gap-3">
            <Link
              href="/cohorts/1"
              className="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/25 transition-all"
            >
              <BookOpen className="h-4 w-4" />
              Enter Classroom
            </Link>
          </div>
        </div>

        {/* Cohort Progress & Capacity Card */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
          {/* Main Progress Card */}
          <div className="lg:col-span-2 p-6 rounded-3xl bg-slate-900/70 border border-slate-800 shadow-xl relative overflow-hidden">
            <div className="flex items-center justify-between mb-4">
              <span className="text-xs font-semibold px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                Cohort Active • Week 2 of 12
              </span>
              <span className="text-xs font-mono text-slate-400">
                Pacing Gate: On Schedule
              </span>
            </div>

            <h2 className="text-xl font-bold text-white mb-2">{cohortData.name}</h2>
            <p className="text-xs text-slate-400 leading-relaxed max-w-xl">
              Immersive curriculum covering modern Next.js 14+ client architecture, Laravel API backend services, 
              real-time Zoom attendance, and automated certificate issuance.
            </p>

            <div className="mt-6">
              <div className="flex items-center justify-between text-xs mb-2">
                <span className="text-slate-300 font-medium">Curriculum Completion</span>
                <span className="text-indigo-400 font-mono font-bold">{cohortData.progressPercentage}%</span>
              </div>
              <div className="h-3 w-full bg-slate-950 rounded-full overflow-hidden border border-slate-800">
                <div
                  className="h-full bg-gradient-to-r from-indigo-500 via-indigo-400 to-cyan-400 rounded-full transition-all duration-500"
                  style={{ width: `${cohortData.progressPercentage}%` }}
                />
              </div>
            </div>

            <div className="mt-6 pt-6 border-t border-slate-800/80 grid grid-cols-3 gap-4 text-center">
              <div>
                <div className="text-lg font-bold text-white">{cohortData.capacity}</div>
                <div className="text-[10px] text-slate-400 uppercase tracking-wider">Cohort Capacity</div>
              </div>
              <div>
                <div className="text-lg font-bold text-emerald-400">{cohortData.availableSlots}</div>
                <div className="text-[10px] text-slate-400 uppercase tracking-wider">Slots Available</div>
              </div>
              <div>
                <div className="text-lg font-bold text-indigo-400">100%</div>
                <div className="text-[10px] text-slate-400 uppercase tracking-wider">Attendance Rate</div>
              </div>
            </div>
          </div>

          {/* Upcoming Live Session Card */}
          <div className="p-6 rounded-3xl bg-gradient-to-br from-indigo-950/40 via-slate-900 to-slate-950 border border-indigo-500/30 shadow-xl flex flex-col justify-between">
            <div>
              <div className="flex items-center justify-between mb-4">
                <div className="h-10 w-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                  <Video className="h-5 w-5" />
                </div>
                <span className="text-xs font-semibold px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                  Zoom Live
                </span>
              </div>

              <div className="text-xs text-indigo-400 font-semibold mb-1">Upcoming Live Classroom</div>
              <h3 className="text-base font-bold text-white leading-snug">{cohortData.nextSession.title}</h3>
              
              <div className="mt-4 space-y-2 text-xs text-slate-400">
                <div className="flex items-center gap-2">
                  <Clock className="h-3.5 w-3.5 text-slate-500" />
                  <span>{cohortData.nextSession.time}</span>
                </div>
                <div className="flex items-center gap-2">
                  <Users className="h-3.5 w-3.5 text-slate-500" />
                  <span>Lead Instructor: {cohortData.instructor}</span>
                </div>
              </div>
            </div>

            <div className="mt-6 pt-4 border-t border-slate-800">
              <button className="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-cyan-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 shadow-lg shadow-cyan-600/20 transition-all">
                <Video className="h-4 w-4" />
                Launch Zoom Classroom
              </button>
            </div>
          </div>
        </div>

        {/* Modules & Lessons Curriculum Breakdown */}
        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <h2 className="text-lg font-bold text-white">Course Modules & Progression</h2>
            <span className="text-xs text-slate-400">Pacing-Gated Chapter Unlock</span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
            {cohortData.modules.map((module) => (
              <div
                key={module.id}
                className="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-slate-700 transition-all flex flex-col justify-between"
              >
                <div>
                  <div className="flex items-center justify-between text-xs text-slate-400 mb-2">
                    <span className="font-mono text-indigo-400">Chapter 0{module.id}</span>
                    <span className="text-emerald-400 font-medium">{module.completed}/{module.total} Completed</span>
                  </div>
                  <h3 className="font-semibold text-white text-sm leading-snug mb-3">{module.title}</h3>

                  <div className="space-y-2 mt-4">
                    {module.lessons.map((lesson) => (
                      <div
                        key={lesson.id}
                        className="flex items-center justify-between p-2.5 rounded-lg bg-slate-950/60 border border-slate-800 text-xs"
                      >
                        <div className="flex items-center gap-2 truncate pr-2">
                          {lesson.completed ? (
                            <CheckCircle2 className="h-3.5 w-3.5 text-emerald-400 shrink-0" />
                          ) : (
                            <PlayCircle className="h-3.5 w-3.5 text-slate-500 shrink-0" />
                          )}
                          <span className={lesson.completed ? "text-slate-300" : "text-slate-400 truncate"}>
                            {lesson.title}
                          </span>
                        </div>
                        <span className="text-[10px] font-mono text-slate-500">{lesson.duration}</span>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="mt-5 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                  <span className="text-slate-500">Auto-attendance logged</span>
                  <Link
                    href="/cohorts/1"
                    className="text-indigo-400 hover:text-indigo-300 font-medium inline-flex items-center gap-1"
                  >
                    View <ChevronRight className="h-3.5 w-3.5" />
                  </Link>
                </div>
              </div>
            ))}
          </div>
        </div>
      </main>
    </div>
  );
}

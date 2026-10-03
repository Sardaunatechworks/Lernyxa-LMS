"use client";

import React, { useState } from "react";
import Link from "next/link";
import {
  GraduationCap,
  PlayCircle,
  CheckCircle2,
  BookOpen,
  ArrowLeft,
  Video,
  FileText,
  Clock,
  ChevronDown,
  ChevronUp,
  Download,
  Share2,
  Check,
} from "lucide-react";

export default function CohortClassroomPage() {
  const [activeLessonId, setActiveLessonId] = useState(1);
  const [completedLessons, setCompletedLessons] = useState<number[]>([1]);
  const [isMarking, setIsMarking] = useState(false);

  const cohort = {
    id: 1,
    name: "Full-Stack Web Engineering — Cohort 1",
    courseTitle: "Modern Full-Stack Development with Next.js & Laravel",
    instructor: "Dr. Aminu Kano",
    capacity: 500,
    enrolledCount: 1,
  };

  const modules = [
    {
      id: 1,
      title: "Module 1: Architecture & Monorepo Scaffolding",
      lessons: [
        {
          id: 1,
          title: "Decoupled LMS Architecture Deep Dive",
          duration: "45 mins",
          type: "video",
          summary:
            "Exploration of the clean decoupled architecture between Next.js 14+ client and Laravel 11+ API services. Explains cookie-based SPA Sanctum authentication, CSRF tokens, and security header configurations.",
        },
        {
          id: 2,
          title: "Docker Compose 7-Service Environment Walkthrough",
          duration: "35 mins",
          type: "video",
          summary:
            "Step-by-step setup of PHP 8.3 FPM, Nginx reverse proxy, PostgreSQL / Supabase, Redis 7 Alpine, MinIO storage, and Mailpit for full offline and production development.",
        },
      ],
    },
    {
      id: 2,
      title: "Module 2: Live Zoom SDK & Automated Attendance",
      lessons: [
        {
          id: 3,
          title: "Zoom Meeting SDK Embedding & Security Tokens",
          duration: "50 mins",
          type: "video",
          summary:
            "Generating Server-to-Server OAuth credentials, calculating HMAC SHA256 signatures, and embedding the Zoom client inside Next.js without requiring third-party browser popups.",
        },
        {
          id: 4,
          title: "Webhooks & Real-time Duration Attendance Logging",
          duration: "40 mins",
          type: "video",
          summary:
            "Capturing Zoom webhook events (`meeting.participant_joined`, `meeting.participant_left`), calculating participation minutes, and updating cohort attendance scores.",
        },
      ],
    },
  ];

  const currentLesson =
    modules.flatMap((m) => m.lessons).find((l) => l.id === activeLessonId) ||
    modules[0].lessons[0];

  const isCurrentCompleted = completedLessons.includes(currentLesson.id);

  const handleMarkComplete = () => {
    setIsMarking(true);
    setTimeout(() => {
      if (!completedLessons.includes(currentLesson.id)) {
        setCompletedLessons([...completedLessons, currentLesson.id]);
      }
      setIsMarking(false);
    }, 400);
  };

  const totalLessons = modules.flatMap((m) => m.lessons).length;
  const progressPercent = Math.round((completedLessons.length / totalLessons) * 100);

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
      {/* Top Navbar */}
      <header className="h-16 border-b border-slate-800 bg-slate-950/80 backdrop-blur-xl px-4 sm:px-6 flex items-center justify-between shrink-0">
        <div className="flex items-center gap-4">
          <Link
            href="/dashboard"
            className="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors"
          >
            <ArrowLeft className="h-4 w-4" />
            Back to Dashboard
          </Link>
          <span className="text-slate-800">|</span>
          <div className="flex items-center gap-2">
            <span className="text-sm font-bold text-white truncate max-w-xs sm:max-w-md">
              {cohort.name}
            </span>
            <span className="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
              Active Cohort
            </span>
          </div>
        </div>

        <div className="flex items-center gap-4">
          <div className="hidden sm:flex items-center gap-2 text-xs">
            <span className="text-slate-400">Progress:</span>
            <span className="font-mono text-indigo-400 font-bold">{progressPercent}%</span>
            <div className="w-24 h-2 bg-slate-900 rounded-full overflow-hidden border border-slate-800">
              <div
                className="h-full bg-indigo-500 rounded-full transition-all duration-300"
                style={{ width: `${progressPercent}%` }}
              />
            </div>
          </div>
        </div>
      </header>

      {/* Classroom Layout */}
      <div className="flex-1 flex flex-col lg:flex-row overflow-hidden">
        {/* Left Sidebar: Curriculum Tree */}
        <aside className="w-full lg:w-80 border-b lg:border-b-0 lg:border-r border-slate-800 bg-slate-900/40 p-4 overflow-y-auto shrink-0">
          <div className="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 px-2">
            <span>Course Syllabus</span>
            <span className="text-indigo-400 font-mono">{totalLessons} Lessons</span>
          </div>

          <div className="space-y-4">
            {modules.map((mod) => (
              <div key={mod.id} className="rounded-2xl border border-slate-800/80 bg-slate-950/60 overflow-hidden">
                <div className="p-3.5 bg-slate-900/60 border-b border-slate-800/80 text-xs font-semibold text-slate-200">
                  {mod.title}
                </div>
                <div className="p-1 space-y-0.5">
                  {mod.lessons.map((lesson) => {
                    const active = lesson.id === activeLessonId;
                    const done = completedLessons.includes(lesson.id);
                    return (
                      <button
                        key={lesson.id}
                        onClick={() => setActiveLessonId(lesson.id)}
                        className={`w-full flex items-center justify-between p-2.5 rounded-xl text-left text-xs transition-all ${
                          active
                            ? "bg-indigo-600 text-white font-medium shadow-md shadow-indigo-600/30"
                            : "hover:bg-slate-900 text-slate-300"
                        }`}
                      >
                        <div className="flex items-center gap-2 truncate pr-2">
                          {done ? (
                            <CheckCircle2
                              className={`h-4 w-4 shrink-0 ${active ? "text-white" : "text-emerald-400"}`}
                            />
                          ) : (
                            <PlayCircle
                              className={`h-4 w-4 shrink-0 ${active ? "text-white" : "text-slate-500"}`}
                            />
                          )}
                          <span className="truncate">{lesson.title}</span>
                        </div>
                        <span className={`text-[10px] font-mono shrink-0 ${active ? "text-indigo-200" : "text-slate-500"}`}>
                          {lesson.duration}
                        </span>
                      </button>
                    );
                  })}
                </div>
              </div>
            ))}
          </div>
        </aside>

        {/* Center / Main Lesson Viewer */}
        <main className="flex-1 overflow-y-auto p-6 lg:p-8 bg-slate-950">
          <div className="max-w-4xl mx-auto space-y-6">
            {/* Video Player Display Placeholder */}
            <div className="aspect-video w-full rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl relative overflow-hidden flex flex-col items-center justify-center group">
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent" />
              <div className="relative z-10 text-center p-6">
                <div className="h-16 w-16 rounded-full bg-indigo-600/90 text-white flex items-center justify-center mx-auto mb-4 shadow-xl shadow-indigo-600/40 group-hover:scale-110 transition-transform cursor-pointer">
                  <PlayCircle className="h-8 w-8" />
                </div>
                <h3 className="text-lg font-bold text-white max-w-md">{currentLesson.title}</h3>
                <p className="text-xs text-slate-400 mt-1 font-mono">Duration: {currentLesson.duration}</p>
              </div>

              <div className="absolute bottom-4 left-6 right-6 flex items-center justify-between text-xs text-slate-400 z-10">
                <span>HD • 1080p Stream</span>
                <span>Zoom / Vimeo Video Gateway</span>
              </div>
            </div>

            {/* Lesson Details & Action Bar */}
            <div className="p-6 rounded-3xl bg-slate-900/60 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <span className="text-[10px] font-mono uppercase tracking-wider text-indigo-400 block mb-1">
                  Lesson Active
                </span>
                <h2 className="text-xl font-bold text-white">{currentLesson.title}</h2>
                <div className="flex items-center gap-3 text-xs text-slate-400 mt-1">
                  <span className="flex items-center gap-1">
                    <Clock className="h-3.5 w-3.5" />
                    {currentLesson.duration}
                  </span>
                  <span>•</span>
                  <span>Lead Instructor: {cohort.instructor}</span>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <button
                  onClick={handleMarkComplete}
                  disabled={isCurrentCompleted || isMarking}
                  className={`inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold shadow-lg transition-all ${
                    isCurrentCompleted
                      ? "bg-emerald-600/20 text-emerald-300 border border-emerald-500/30 cursor-default"
                      : "bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-600/25 hover:scale-[1.02]"
                  }`}
                >
                  {isCurrentCompleted ? (
                    <>
                      <Check className="h-4 w-4 text-emerald-400" />
                      Completed
                    </>
                  ) : isMarking ? (
                    "Recording Progress..."
                  ) : (
                    <>
                      <CheckCircle2 className="h-4 w-4" />
                      Mark as Complete
                    </>
                  )}
                </button>
              </div>
            </div>

            {/* Lesson Notes & Overview */}
            <div className="p-6 rounded-3xl bg-slate-900/40 border border-slate-800 space-y-4 text-slate-300 text-sm leading-relaxed">
              <h3 className="font-bold text-base text-white">Lesson Summary & Objectives</h3>
              <p>{currentLesson.summary}</p>
              <div className="p-4 rounded-2xl bg-indigo-950/30 border border-indigo-500/20 text-xs text-indigo-300">
                💡 <strong>Cohort Note:</strong> Remember to attend the live Zoom session tomorrow at 6:00 PM (WAT). Attendance duration will be automatically recorded toward your certificate eligibility.
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}

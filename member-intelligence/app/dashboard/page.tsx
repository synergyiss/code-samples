"use client";

import Image from "next/image";
import Link from "next/link";
import { useEffect } from "react"
import { Chart } from "chart.js";

export default function Home() {
  /*
  Using demo data until database access is finnished.
  */
  useEffect(() => {
    const memberSatisfactionChartctx = document.getElementById('memberSatisfactionChart').getContext('2d');
    let memberSatisfactionChart = new Chart(memberSatisfactionChartctx, {
      type: 'line',
      data: {
        labels: ["5/1","5/2","5/3","5/4","5/5","5/6"],
        datasets: [{
          data: [52,48,37,28,15],
          label: "Satisfied",
          borderColor: "#3e95cd",
          backgroundColor: "#7bb6dd",
          fill: false,
        }, {
          data: [28,35,40,62,73],
          label: "Unsatisfied",
          borderColor: "#3cba9f",
          backgroundColor: "#71d1bd",
          fill: false,
        }, {
          data: [15,8,7,3,1],
          label: "No Answer",
          borderColor: "#ffa500",
          backgroundColor: "#ffc04d",
          fill: false,
        }
        ]
      },
    });
  }, [])
  useEffect(() => {
    var memberAttendanceChartctx = document.getElementById('memberAttendanceChart').getContext('2d');
    var memberAttendanceChart = new Chart(memberAttendanceChartctx, {
      type: 'line',
      data: {
        labels: ["5/1","5/2","5/3","5/4","5/5","5/6"],
        datasets: [{
          data: [35,40,57,75,30],
          label: "Main Dining Room",
          borderColor: "#3e95cd",
          backgroundColor: "#7bb6dd",
          fill: false,
        }, {
          data: [40,55,70,85,100],
          label: "Pub",
          borderColor: "#3cba9f",
          backgroundColor: "#71d1bd",
          fill: false,
        }
        ]
      },
    });
  }, [])
  return (
<div className="flex h-screen bg-black">
<div className="w-screen h-screen flex flex-col justify-center items-center">
      <Image
    width={485}
    height={92}
    src="/logo.png"
    alt="Synergy Innovative Systems"
    className="w-485 h-92"
  />
            <Link
            href="/"
            prefetch={false}
            className="text-stone-400 underline hover:text-stone-200 transition-all"
          >
            Main Page
          </Link>

      <h1 className="text-stone-200 font-bold text-2xl">Member Satisfaction</h1>
      <div className="w-[400px] h-screen flex mx-auto my-auto">
        <div className='border border-gray-400 pt-0 rounded-xl  w-full h-fit my-auto  shadow-xl'>
          <canvas id='memberSatisfactionChart'></canvas>
        </div>
      </div>
      <h1 className="text-stone-200 font-bold text-2xl">Member Attendance</h1>
      <div className="w-[400px] h-screen flex mx-auto my-auto">
        <div className='border border-gray-400 pt-0 rounded-xl  w-full h-fit my-auto  shadow-xl'>
          <canvas id='memberAttendanceChart'></canvas>
        </div>
      </div>
    </div>
    </div>
  )
}

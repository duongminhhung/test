import { createRoot } from 'react-dom/client'
import { useEffect, useState } from 'react'
import OeeCard from './oee/components/OeeCard'
import { Toast } from 'react-bootstrap'
import styles from '../scss/dashboard.module.scss'

const Dashboard = () => {
    const [toastList, setToastList] = useState([])
    
    useEffect(() => {
        const interval = setInterval(() => {
            setToastList(prev => [...prev, {
                title: `toast ${Math.round(Math.random() * 100)}`,
                message: 'test message'
            }])
            setToastList(prev => {
                if(prev.length > 4) {
                    prev.shift()
                    return [...prev]
                }
                return prev
            })
        }, 2000)

        return () => clearInterval(interval)
    },[])

    return (
        <div className={styles['container']}>
            <div className='row'>
                <div className='col-3'>
                    <OeeCard title='OEE' value={0} />
                </div>
                <div className='col-3'>
                    <OeeCard title='Availability' value={0} />
                </div>
                <div className='col-3'>
                    <OeeCard title='Performance' value={0} />
                </div>  
                <div className='col-3'>
                    <OeeCard title='Quality' value={0} />
                </div>
            </div>
            <div
                id='carouselExampleControls'
                className={`carousel slide h-100 mt-3 ${styles['carousel']}`}
                data-ride='carousel'
            >
                <div className='carousel-inner h-100'>
                    <div className='carousel-item h-100 bg-gray active'>
                        <div className='text-center'>1</div>
                    </div>
                    <div className='carousel-item h-100 bg-gray'>
                        <div className='text-center'>2</div>
                    </div>
                    <div className='carousel-item h-100 bg-gray'>
                        <div className='text-center'>3</div>
                    </div>
                </div>
            </div>
            <div className={styles['toast-container']}>
                {toastList.map(toast => {
                    return (
                        <Toast
                            show={true}
                            className={styles['toast']}
                        >
                            <Toast.Header>{toast.title}</Toast.Header>
                            <Toast.Body>{toast.message}</Toast.Body>
                        </Toast>
                    )
                })}
            </div>
        </div>
    )
}

const root = createRoot(document.getElementById('app'))
root.render(<Dashboard />)